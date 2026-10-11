<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('DriveEase');
    }

    public function test_vehicles_page_loads(): void
    {
        $response = $this->get('/vehicles');
        $response->assertStatus(200);
    }

    public function test_about_page_loads(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
    }

    public function test_contact_page_loads(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
    }

    public function test_faq_page_loads(): void
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
    }

    public function test_forgot_password_page_loads(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }
}
