<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Featured vehicles — most rented, available only
        $featuredVehicles = collect(DB::select("
            SELECT v.*, c.category_name,
                   (SELECT vi.image FROM vehicle_images vi
                     WHERE vi.vehicle_id = v.id
                     ORDER BY vi.is_primary DESC, vi.sort_order ASC LIMIT 1) AS primary_image,
                   (SELECT COUNT(*) FROM reviews r WHERE r.vehicle_id = v.id AND r.is_approved = 1) AS review_count
            FROM vehicles v
            INNER JOIN vehicle_categories c ON c.id = v.category_id
            WHERE v.status = 'available'
            ORDER BY v.total_rentals DESC
            LIMIT 8
        "));

        // Categories with available vehicle counts
        $categories = DB::select("
            SELECT c.*,
                   (SELECT COUNT(*) FROM vehicles v WHERE v.category_id = c.id AND v.status = 'available') AS available_count
            FROM vehicle_categories c
            WHERE c.is_active = 1
            ORDER BY c.sort_order ASC
            LIMIT 8
        ");

        // Site-wide stats
        $vehicleCount  = DB::select("SELECT COUNT(*) AS total FROM vehicles")[0]->total;
        $customerCount = DB::select("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'")[0]->total;
        $bookingCount  = DB::select("SELECT COUNT(*) AS total FROM bookings WHERE booking_status = 'completed'")[0]->total;

        $stats = [
            'vehicles'  => $vehicleCount,
            'customers' => $customerCount,
            'bookings'  => $bookingCount,
            'cities'    => 25,
        ];

        // Testimonials — top rated approved reviews
        $testimonials = collect(DB::select("
            SELECT r.*, u.name AS user_name, u.avatar AS user_avatar
            FROM reviews r
            INNER JOIN users u ON u.id = r.user_id
            WHERE r.is_approved = 1 AND r.rating >= 4
            ORDER BY r.created_at DESC
            LIMIT 6
        "));

        return view('home.index', compact('featuredVehicles', 'categories', 'stats', 'testimonials'));
    }

    public function about()
    {
        return view('home.about');
    }

    public function contact()
    {
        return view('home.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        DB::insert("
            INSERT INTO contact_messages (name, email, phone, subject, message, is_read, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())
        ", [
            $request->name,
            $request->email,
            $request->phone,
            $request->subject,
            $request->message,
        ]);

        return back()->with('success', 'Your message has been sent. We will get back to you shortly!');
    }

    public function faq()
    {
        return view('home.faq');
    }

    public function privacy()
    {
        return view('home.privacy');
    }

    public function terms()
    {
        return view('home.terms');
    }

    public function newsletter(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Manual "firstOrCreate" using raw SQL
        $existing = DB::select("SELECT id FROM newsletter_subscriptions WHERE email = ?", [$request->email]);

        if (empty($existing)) {
            DB::insert("
                INSERT INTO newsletter_subscriptions (email, is_active, created_at, updated_at)
                VALUES (?, 1, NOW(), NOW())
            ", [$request->email]);
        }

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
