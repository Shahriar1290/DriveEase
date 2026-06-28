<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        // Build WHERE clause dynamically but always with bound parameters
        $where    = ["v.status = 'available'"];
        $bindings = [];

        if ($search = $request->get('search')) {
            $where[]    = "(v.vehicle_name LIKE ? OR v.brand LIKE ? OR v.model LIKE ?)";
            $like       = "%{$search}%";
            $bindings[] = $like;
            $bindings[] = $like;
            $bindings[] = $like;
        }
        if ($category = $request->get('category')) {
            $where[]    = "v.category_id = ?";
            $bindings[] = $category;
        }
        if ($fuel = $request->get('fuel_type')) {
            $where[]    = "v.fuel_type = ?";
            $bindings[] = $fuel;
        }
        if ($trans = $request->get('transmission')) {
            $where[]    = "v.transmission = ?";
            $bindings[] = $trans;
        }
        if ($min = $request->get('min_price')) {
            $where[]    = "v.price_per_day >= ?";
            $bindings[] = $min;
        }
        if ($max = $request->get('max_price')) {
            $where[]    = "v.price_per_day <= ?";
            $bindings[] = $max;
        }

        $whereSql = implode(' AND ', $where);

        $orderSql = match ($request->get('sort', 'popular')) {
            'price_low'  => 'v.price_per_day ASC',
            'price_high' => 'v.price_per_day DESC',
            'newest'     => 'v.created_at DESC',
            default      => 'v.total_rentals DESC',
        };

        $countRow = DB::select("SELECT COUNT(*) AS total FROM vehicles v WHERE {$whereSql}", $bindings);
        $total    = $countRow[0]->total;

        $rows = DB::select("
            SELECT v.*, c.category_name,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image,
                   (SELECT COUNT(*) FROM reviews r WHERE r.vehicle_id = v.id AND r.is_approved = 1) AS review_count
            FROM vehicles v
            INNER JOIN vehicle_categories c ON c.id = v.category_id
            WHERE {$whereSql}
            ORDER BY {$orderSql}
            LIMIT {$perPage} OFFSET {$offset}
        ", $bindings);

        $vehicles = $this->makePaginator($rows, $total, $perPage, $page, $request);

        $categories = DB::select("
            SELECT c.*, (SELECT COUNT(*) FROM vehicles v WHERE v.category_id = c.id) AS vehicles_count
            FROM vehicle_categories c
            WHERE c.is_active = 1
        ");

        $maxPriceRow = DB::select("SELECT MAX(price_per_day) AS max_price FROM vehicles WHERE status = 'available'");
        $maxPrice    = $maxPriceRow[0]->max_price ?? 1000;

        return view('home.vehicles', compact('vehicles', 'categories', 'maxPrice'));
    }

    public function show(string $slug)
    {
        $vehicle = DB::select("
            SELECT v.*, c.category_name, c.id AS category_id_ref
            FROM vehicles v
            INNER JOIN vehicle_categories c ON c.id = v.category_id
            WHERE v.slug = ?
            LIMIT 1
        ", [$slug]);

        abort_if(empty($vehicle), 404);
        $vehicle = $vehicle[0];

        $images = DB::select("
            SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY sort_order ASC
        ", [$vehicle->id]);

        $reviews = DB::select("
            SELECT r.*, u.name AS user_name, u.avatar AS user_avatar
            FROM reviews r
            INNER JOIN users u ON u.id = r.user_id
            WHERE r.vehicle_id = ? AND r.is_approved = 1
            ORDER BY r.created_at DESC
            LIMIT 5
        ", [$vehicle->id]);

        $reviewCount = DB::select("SELECT COUNT(*) AS total FROM reviews WHERE vehicle_id = ? AND is_approved = 1", [$vehicle->id])[0]->total;

        $relatedVehicles = DB::select("
            SELECT v.*,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM vehicles v
            WHERE v.status = 'available' AND v.category_id = ? AND v.id != ?
            LIMIT 4
        ", [$vehicle->category_id, $vehicle->id]);

        $userHasBookedVehicle = false;
        if (auth()->check()) {
            $check = DB::select("
                SELECT id FROM bookings
                WHERE user_id = ? AND vehicle_id = ? AND booking_status = 'completed'
                LIMIT 1
            ", [auth()->id(), $vehicle->id]);
            $userHasBookedVehicle = !empty($check);
        }

        return view('home.vehicle-detail', compact('vehicle', 'images', 'reviews', 'reviewCount', 'relatedVehicles', 'userHasBookedVehicle'));
    }

    public function compare(Request $request)
    {
        $ids = array_filter(array_slice((array) $request->get('ids', []), 0, 4));

        $vehicles = [];
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $vehicles = DB::select("
                SELECT v.*, c.category_name,
                       (SELECT vi.image FROM vehicle_images vi
                         WHERE vi.vehicle_id = v.id
                         ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image,
                       (SELECT COUNT(*) FROM reviews r WHERE r.vehicle_id = v.id AND r.is_approved = 1) AS review_count
                FROM vehicles v
                INNER JOIN vehicle_categories c ON c.id = v.category_id
                WHERE v.id IN ({$placeholders})
            ", $ids);
        }

        return view('home.compare', compact('vehicles'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $like  = "%{$query}%";

        $rows = DB::select("
            SELECT v.id, v.vehicle_name, v.brand, v.price_per_day, v.slug,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM vehicles v
            WHERE v.status = 'available'
              AND (v.vehicle_name LIKE ? OR v.brand LIKE ? OR v.model LIKE ?)
            LIMIT 8
        ", [$like, $like, $like]);

        $results = array_map(function ($v) {
            return [
                'id'    => $v->id,
                'name'  => $v->vehicle_name,
                'brand' => $v->brand,
                'price' => $v->price_per_day,
                'image' => $v->primary_image ? asset('storage/' . $v->primary_image) : asset('images/vehicle-default.jpg'),
                'url'   => route('vehicles.show', $v->slug),
            ];
        }, $rows);

        return response()->json($results);
    }

    /**
     * Build a minimal LengthAwarePaginator-compatible object from raw rows,
     * so existing Blade views ($vehicles->links(), ->total(), etc.) keep working.
     */
    private function makePaginator(array $items, int $total, int $perPage, int $page, Request $request)
    {
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
