<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    public function test_customer_can_view_booking_list(): void
    {
        $user = User::where('role', 'customer')->first();
        if (!$user) {
            $this->markTestSkipped('No customer user found.');
        }

        $this->actingAs($user);
        $response = $this->get(route('customer.bookings.index'));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_booking_create(): void
    {
        $vehicle = Vehicle::where('status', 'available')->first();
        if (!$vehicle) {
            $this->markTestSkipped('No available vehicle found.');
        }

        $response = $this->get(route('customer.bookings.create', $vehicle->slug));
        $response->assertRedirect('/login');
    }

    public function test_customer_can_view_booking_create_form(): void
    {
        $user = User::where('role', 'customer')->first();
        $vehicle = Vehicle::where('status', 'available')->first();

        if (!$user || !$vehicle) {
            $this->markTestSkipped('Missing test data.');
        }

        $this->actingAs($user);
        $response = $this->get(route('customer.bookings.create', $vehicle->slug));
        $response->assertStatus(200);
    }

    public function test_customer_can_create_booking(): void
    {
        $user = User::where('role', 'customer')->first();
        $vehicle = Vehicle::where('status', 'available')->first();

        if (!$user || !$vehicle) {
            $this->markTestSkipped('Missing test data.');
        }

        $this->actingAs($user);

        $pickup = now()->addDays(30 + rand(1, 30))->format('Y-m-d');
        $return = now()->addDays(30 + rand(31, 60))->format('Y-m-d');

        $response = $this->post(route('customer.bookings.store'), [
            'vehicle_id' => $vehicle->id,
            'pickup_date' => $pickup,
            'return_date' => $return,
            'pickup_location' => 'Dhaka Office',
            'return_location' => 'Dhaka Office',
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect(route('customer.bookings.index'));
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'booking_status' => 'pending',
        ]);
    }

    public function test_admin_can_approve_booking(): void
    {
        $admin = User::where('role', 'admin')->first();
        $booking = Booking::where('booking_status', 'pending')->first();

        if (!$admin || !$booking) {
            $this->markTestSkipped('Missing test data.');
        }

        $this->actingAs($admin);
        $response = $this->post(route('admin.bookings.approve', $booking->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'approved',
        ]);
    }

    public function test_admin_reject_requires_pending_status(): void
    {
        $admin = User::where('role', 'admin')->first();
        $booking = Booking::where('booking_status', 'pending')->first();

        if (!$admin || !$booking) {
            $this->markTestSkipped('Missing test data.');
        }

        $this->actingAs($admin);
        $response = $this->post(route('admin.bookings.reject', $booking->id), [
            'admin_notes' => 'Test rejection reason',
        ]);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_status' => 'rejected',
        ]);
    }
}
