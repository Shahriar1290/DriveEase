<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_guest_redirected_from_admin(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin(): void
    {
        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $this->markTestSkipped('No customer user found.');
        }

        $this->actingAs($customer);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $this->actingAs($admin);
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_vehicles_index(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $this->actingAs($admin);
        $response = $this->get(route('admin.vehicles.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_bookings_index(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $this->actingAs($admin);
        $response = $this->get(route('admin.bookings.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_customers_index(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $this->actingAs($admin);
        $response = $this->get(route('admin.customers.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_access_payments_index(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('No admin user found.');
        }

        $this->actingAs($admin);
        $response = $this->get(route('admin.payments.index'));
        $response->assertStatus(200);
    }
}
