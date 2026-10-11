<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    public function test_register_page_loads(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Create Account');
    }

    public function test_customer_can_register(): void
    {
        $email = 'testuser_' . uniqid() . '@example.com';
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => $email,
            'phone' => '+880 1700000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertDatabaseHas('users', ['email' => $email, 'role' => 'customer']);
    }

    public function test_invalid_credentials_rejected(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_customer_can_login(): void
    {
        $user = User::where('role', 'customer')->first();
        if (!$user) {
            $this->markTestSkipped('No customer user found in seed data.');
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_admin_can_login(): void
    {
        $user = User::where('role', 'admin')->first();
        if (!$user) {
            $this->markTestSkipped('No admin user found in seed data.');
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('role', 'customer')->first();
        if (!$user) {
            $this->markTestSkipped('No customer user found in seed data.');
        }

        $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }
}
