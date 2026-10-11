<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * WishlistComposer
 * ---------------------------------------------------------------------
 * Shares the logged-in customer's wishlisted vehicle IDs with the public
 * vehicle listing views so the heart buttons on vehicle cards can show
 * whether a vehicle is already saved. Guests and admins get an empty list.
 * ---------------------------------------------------------------------
 */
class WishlistComposer
{
    public function compose(View $view): void
    {
        $ids = [];

        if (auth()->check() && auth()->user()->isCustomer()) {
            $ids = array_map(
                fn ($row) => (int) $row->vehicle_id,
                DB::select('SELECT vehicle_id FROM wishlists WHERE user_id = ?', [auth()->id()])
            );
        }

        $view->with('wishlistIds', $ids);
    }
}
