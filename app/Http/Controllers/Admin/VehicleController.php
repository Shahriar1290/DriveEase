<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $where    = ['1=1'];
        $bindings = [];

        if ($search = $request->search) {
            $where[]    = "(v.vehicle_name LIKE ? OR v.brand LIKE ? OR v.registration_number LIKE ?)";
            $like       = "%{$search}%";
            $bindings[] = $like; $bindings[] = $like; $bindings[] = $like;
        }
        if ($cat = $request->category_id) {
            $where[]    = "v.category_id = ?";
            $bindings[] = $cat;
        }
        if ($status = $request->status) {
            $where[]    = "v.status = ?";
            $bindings[] = $status;
        }
        $whereSql = implode(' AND ', $where);

        $total = DB::select("SELECT COUNT(*) AS total FROM vehicles v WHERE {$whereSql}", $bindings)[0]->total;

        $rows = DB::select("
            SELECT v.*, c.category_name,
                   (SELECT COUNT(*) FROM bookings b WHERE b.vehicle_id = v.id) AS bookings_count,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM vehicles v
            INNER JOIN vehicle_categories c ON c.id = v.category_id
            WHERE {$whereSql}
            ORDER BY v.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $bindings);

        $vehicles = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $categories = DB::select("SELECT * FROM vehicle_categories ORDER BY category_name ASC");

        return view('admin.vehicles.index', compact('vehicles', 'categories'));
    }

    public function create()
    {
        $categories = DB::select("SELECT * FROM vehicle_categories ORDER BY category_name ASC");
        return view('admin.vehicles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'         => 'required|integer',
            'vehicle_name'        => 'required|string|max:255',
            'brand'                => 'required|string|max:100',
            'model'                => 'required|string|max:100',
            'year'                 => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'registration_number'  => 'required|string',
            'description'          => 'nullable|string',
            'fuel_type'            => 'required|in:petrol,diesel,electric,hybrid,cng',
            'transmission'         => 'required|in:manual,automatic,semi-automatic',
            'seating_capacity'     => 'required|integer|min:1|max:50',
            'price_per_day'        => 'required|numeric|min:0',
            'status'               => 'required|in:available,rented,maintenance,inactive',
            'color'                => 'nullable|string|max:50',
            'mileage'              => 'nullable|string|max:50',
            'engine_cc'            => 'nullable|string|max:50',
            'images.*'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        // Manual uniqueness check on registration_number
        $exists = DB::select("SELECT id FROM vehicles WHERE registration_number = ?", [$data['registration_number']]);
        if (!empty($exists)) {
            return back()->withErrors(['registration_number' => 'This registration number is already in use.'])->withInput();
        }

        $slug = Str::slug($data['vehicle_name'] . '-' . $data['registration_number']);

        DB::insert("
            INSERT INTO vehicles (
                category_id, vehicle_name, slug, brand, model, year, registration_number,
                description, fuel_type, transmission, seating_capacity, price_per_day, status,
                color, mileage, engine_cc, air_conditioning, gps, bluetooth, usb_charger, child_seat,
                total_rentals, average_rating, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW(), NOW())
        ", [
            $data['category_id'], $data['vehicle_name'], $slug, $data['brand'], $data['model'],
            $data['year'] ?? null, $data['registration_number'], $data['description'] ?? null,
            $data['fuel_type'], $data['transmission'], $data['seating_capacity'], $data['price_per_day'],
            $data['status'], $data['color'] ?? null, $data['mileage'] ?? null, $data['engine_cc'] ?? null,
            $request->boolean('air_conditioning') ? 1 : 0,
            $request->boolean('gps') ? 1 : 0,
            $request->boolean('bluetooth') ? 1 : 0,
            $request->boolean('usb_charger') ? 1 : 0,
            $request->boolean('child_seat') ? 1 : 0,
        ]);

        $vehicleId = DB::getPdo()->lastInsertId();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                DB::insert("
                    INSERT INTO vehicle_images (vehicle_id, image, is_primary, sort_order, created_at, updated_at)
                    VALUES (?, ?, ?, ?, NOW(), NOW())
                ", [$vehicleId, $path, $index === 0 ? 1 : 0, $index]);
            }
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    public function show(int $id)
    {
        $vehicle = DB::select("
            SELECT v.*, c.category_name FROM vehicles v
            INNER JOIN vehicle_categories c ON c.id = v.category_id
            WHERE v.id = ? LIMIT 1
        ", [$id]);
        abort_if(empty($vehicle), 404);
        $vehicle = $vehicle[0];

        $images = DB::select("SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY sort_order ASC", [$id]);

        $bookings = DB::select("
            SELECT b.*, u.name AS user_name
            FROM bookings b
            INNER JOIN users u ON u.id = b.user_id
            WHERE b.vehicle_id = ?
            ORDER BY b.created_at DESC
            LIMIT 8
        ", [$id]);

        $maintenanceRecords = DB::select("SELECT * FROM maintenance_records WHERE vehicle_id = ? ORDER BY maintenance_date DESC", [$id]);

        $reviews = DB::select("
            SELECT r.*, u.name AS user_name
            FROM reviews r
            INNER JOIN users u ON u.id = r.user_id
            WHERE r.vehicle_id = ?
            ORDER BY r.created_at DESC
            LIMIT 5
        ", [$id]);

        $approvedReviewCount = DB::select("SELECT COUNT(*) AS total FROM reviews WHERE vehicle_id = ? AND is_approved = 1", [$id])[0]->total;
        $allReviewCount      = DB::select("SELECT COUNT(*) AS total FROM reviews WHERE vehicle_id = ?", [$id])[0]->total;

        return view('admin.vehicles.show', compact(
            'vehicle', 'images', 'bookings', 'maintenanceRecords', 'reviews', 'approvedReviewCount', 'allReviewCount'
        ));
    }

    public function edit(int $id)
    {
        $vehicle = DB::select("SELECT * FROM vehicles WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($vehicle), 404);
        $vehicle = $vehicle[0];
        $vehicle->images = DB::select("SELECT * FROM vehicle_images WHERE vehicle_id = ? ORDER BY sort_order ASC", [$id]);

        $categories = DB::select("SELECT * FROM vehicle_categories ORDER BY category_name ASC");

        return view('admin.vehicles.edit', compact('vehicle', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $existing = DB::select("SELECT * FROM vehicles WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($existing), 404);

        $data = $request->validate([
            'category_id'         => 'required|integer',
            'vehicle_name'        => 'required|string|max:255',
            'brand'                => 'required|string|max:100',
            'model'                => 'required|string|max:100',
            'year'                 => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'registration_number'  => 'required|string',
            'description'          => 'nullable|string',
            'fuel_type'            => 'required|in:petrol,diesel,electric,hybrid,cng',
            'transmission'         => 'required|in:manual,automatic,semi-automatic',
            'seating_capacity'     => 'required|integer|min:1|max:50',
            'price_per_day'        => 'required|numeric|min:0',
            'status'               => 'required|in:available,rented,maintenance,inactive',
            'color'                => 'nullable|string|max:50',
            'mileage'              => 'nullable|string|max:50',
            'engine_cc'            => 'nullable|string|max:50',
            'new_images.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $regTaken = DB::select(
            "SELECT id FROM vehicles WHERE registration_number = ? AND id != ?",
            [$data['registration_number'], $id]
        );
        if (!empty($regTaken)) {
            return back()->withErrors(['registration_number' => 'This registration number is already in use.'])->withInput();
        }

        DB::update("
            UPDATE vehicles SET
                category_id = ?, vehicle_name = ?, brand = ?, model = ?, year = ?, registration_number = ?,
                description = ?, fuel_type = ?, transmission = ?, seating_capacity = ?, price_per_day = ?,
                status = ?, color = ?, mileage = ?, engine_cc = ?,
                air_conditioning = ?, gps = ?, bluetooth = ?, usb_charger = ?, child_seat = ?,
                updated_at = NOW()
            WHERE id = ?
        ", [
            $data['category_id'], $data['vehicle_name'], $data['brand'], $data['model'],
            $data['year'] ?? null, $data['registration_number'], $data['description'] ?? null,
            $data['fuel_type'], $data['transmission'], $data['seating_capacity'], $data['price_per_day'],
            $data['status'], $data['color'] ?? null, $data['mileage'] ?? null, $data['engine_cc'] ?? null,
            $request->boolean('air_conditioning') ? 1 : 0,
            $request->boolean('gps') ? 1 : 0,
            $request->boolean('bluetooth') ? 1 : 0,
            $request->boolean('usb_charger') ? 1 : 0,
            $request->boolean('child_seat') ? 1 : 0,
            $id,
        ]);

        // Remove deleted images
        if ($request->delete_images) {
            foreach ($request->delete_images as $imgId) {
                $img = DB::select("SELECT * FROM vehicle_images WHERE id = ? AND vehicle_id = ? LIMIT 1", [$imgId, $id]);
                if (!empty($img)) {
                    Storage::disk('public')->delete($img[0]->image);
                    DB::delete("DELETE FROM vehicle_images WHERE id = ?", [$imgId]);
                }
            }
        }

        // Add new images
        if ($request->hasFile('new_images')) {
            $existingCount = DB::select("SELECT COUNT(*) AS total FROM vehicle_images WHERE vehicle_id = ?", [$id])[0]->total;
            foreach ($request->file('new_images') as $index => $file) {
                $path = $file->store('vehicles', 'public');
                DB::insert("
                    INSERT INTO vehicle_images (vehicle_id, image, is_primary, sort_order, created_at, updated_at)
                    VALUES (?, ?, ?, ?, NOW(), NOW())
                ", [$id, $path, ($existingCount === 0 && $index === 0) ? 1 : 0, $existingCount + $index]);
            }
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(int $id)
    {
        $images = DB::select("SELECT * FROM vehicle_images WHERE vehicle_id = ?", [$id]);
        foreach ($images as $img) {
            Storage::disk('public')->delete($img->image);
        }

        // Foreign keys cascade in DB schema, but we delete explicitly for clarity
        DB::delete("DELETE FROM vehicle_images WHERE vehicle_id = ?", [$id]);
        DB::delete("DELETE FROM vehicles WHERE id = ?", [$id]);

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
