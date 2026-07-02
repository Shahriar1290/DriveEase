<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 15;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;

        $where    = ['1=1'];
        $bindings = [];

        if ($request->status === 'approved') {
            $where[] = "r.is_approved = 1";
        }
        if ($request->status === 'pending') {
            $where[] = "r.is_approved = 0";
        }
        if ($s = $request->search) {
            $where[]    = "v.vehicle_name LIKE ?";
            $bindings[] = "%{$s}%";
        }
        $whereSql = implode(' AND ', $where);

        $total = DB::select("
            SELECT COUNT(*) AS total FROM reviews r
            INNER JOIN vehicles v ON v.id = r.vehicle_id
            WHERE {$whereSql}
        ", $bindings)[0]->total;

        $rows = DB::select("
            SELECT r.*, u.name AS user_name, v.vehicle_name, v.id AS vehicle_id_ref
            FROM reviews r
            INNER JOIN users u ON u.id = r.user_id
            INNER JOIN vehicles v ON v.id = r.vehicle_id
            WHERE {$whereSql}
            ORDER BY r.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", $bindings);

        $reviews = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(int $id)
    {
        $review = DB::select("SELECT * FROM reviews WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($review), 404);

        DB::update("UPDATE reviews SET is_approved = 1, updated_at = NOW() WHERE id = ?", [$id]);
        $this->recalculateVehicleRating($review[0]->vehicle_id);

        return back()->with('success', 'Review approved.');
    }

    public function reject(int $id)
    {
        $review = DB::select("SELECT * FROM reviews WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($review), 404);

        DB::update("UPDATE reviews SET is_approved = 0, updated_at = NOW() WHERE id = ?", [$id]);
        $this->recalculateVehicleRating($review[0]->vehicle_id);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(int $id)
    {
        $review = DB::select("SELECT * FROM reviews WHERE id = ? LIMIT 1", [$id]);
        abort_if(empty($review), 404);
        $vehicleId = $review[0]->vehicle_id;

        DB::delete("DELETE FROM reviews WHERE id = ?", [$id]);
        $this->recalculateVehicleRating($vehicleId);

        return back()->with('success', 'Review deleted.');
    }

    /**
     * Recalculate and persist a vehicle's average_rating using raw SQL
     * (replaces the old Eloquent Vehicle::updateRating() model method).
     */
    private function recalculateVehicleRating(int $vehicleId): void
    {
        $avgRow = DB::select("
            SELECT COALESCE(AVG(rating), 0) AS avg_rating
            FROM reviews
            WHERE vehicle_id = ? AND is_approved = 1
        ", [$vehicleId]);

        $avg = round($avgRow[0]->avg_rating ?? 0, 2);

        DB::update("UPDATE vehicles SET average_rating = ?, updated_at = NOW() WHERE id = ?", [$avg, $vehicleId]);
    }
}
