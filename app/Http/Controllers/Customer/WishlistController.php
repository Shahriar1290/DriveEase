<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;
        $page    = max(1, (int) $request->get('page', 1));
        $offset  = ($page - 1) * $perPage;
        $userId  = auth()->id();

        $total = DB::select("SELECT COUNT(*) AS total FROM wishlists WHERE user_id = ?", [$userId])[0]->total;

        $rows = DB::select("
            SELECT w.id AS wishlist_id, w.vehicle_id, w.created_at AS wishlisted_at,
                   v.*, c.category_name,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image
            FROM wishlists w
            INNER JOIN vehicles v ON v.id = w.vehicle_id
            INNER JOIN vehicle_categories c ON c.id = v.category_id
            WHERE w.user_id = ?
            ORDER BY w.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ", [$userId]);

        $wishlists = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('customer.dashboard.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['vehicle_id' => 'required|integer']);

        $userId    = auth()->id();
        $vehicleId = $request->vehicle_id;

        $existing = DB::select(
            "SELECT id FROM wishlists WHERE user_id = ? AND vehicle_id = ? LIMIT 1",
            [$userId, $vehicleId]
        );

        if (!empty($existing)) {
            DB::delete("DELETE FROM wishlists WHERE id = ?", [$existing[0]->id]);
            $inWishlist = false;
            $message    = 'Removed from wishlist.';
        } else {
            DB::insert("
                INSERT INTO wishlists (user_id, vehicle_id, created_at, updated_at)
                VALUES (?, ?, NOW(), NOW())
            ", [$userId, $vehicleId]);
            $inWishlist = true;
            $message    = 'Added to wishlist.';
        }

        if ($request->ajax()) {
            return response()->json(['in_wishlist' => $inWishlist, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
