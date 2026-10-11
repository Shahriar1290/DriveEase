<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlists = Wishlist::with(['vehicle.category:id,category_name', 'vehicle.firstImage'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('customer.dashboard.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['vehicle_id' => 'required|integer']);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('vehicle_id', $request->vehicle_id)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message = 'Removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'vehicle_id' => $request->vehicle_id,
            ]);
            $inWishlist = true;
            $message = 'Added to wishlist.';
        }

        if ($request->ajax()) {
            return response()->json(['in_wishlist' => $inWishlist, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
