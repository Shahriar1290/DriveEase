<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Wishlist;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    public function test_customer_can_toggle_wishlist_via_ajax_and_listing_reflects_it(): void
    {
        $user = User::where('role', 'customer')->first();
        $vehicle = Vehicle::where('status', 'available')->first();

        if (!$user || !$vehicle) {
            $this->markTestSkipped('Missing test data.');
        }

        // Start from a known state
        Wishlist::where('user_id', $user->id)->where('vehicle_id', $vehicle->id)->delete();
        $this->actingAs($user);

        $this->postJson(route('customer.wishlist.toggle'), ['vehicle_id' => $vehicle->id], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJson(['in_wishlist' => true]);

        $this->get(route('vehicles.index'))
            ->assertOk()
            ->assertViewHas('wishlistIds', fn ($ids) => in_array((int) $vehicle->id, $ids, true));

        $this->postJson(route('customer.wishlist.toggle'), ['vehicle_id' => $vehicle->id], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJson(['in_wishlist' => false]);

        $this->get(route('vehicles.index'))
            ->assertViewHas('wishlistIds', fn ($ids) => !in_array((int) $vehicle->id, $ids, true));
    }

    public function test_guest_gets_empty_wishlist_ids(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertViewHas('wishlistIds', []);
    }
}
