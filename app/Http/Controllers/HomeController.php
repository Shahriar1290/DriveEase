<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredVehicles = Vehicle::available()
            ->with('category')
            ->withCount(['reviews as review_count' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->with('firstImage')
            ->orderByDesc('total_rentals')
            ->limit(8)
            ->get();

        $categories = VehicleCategory::where('is_active', true)
            ->withCount(['vehicles as available_count' => function ($q) {
                $q->where('status', 'available');
            }])
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        $stats = [
            'vehicles'  => Vehicle::count(),
            'customers' => User::where('role', 'customer')->count(),
            'bookings'  => Booking::where('booking_status', 'completed')->count(),
            'cities'    => 25,
        ];

        $testimonials = Review::where('is_approved', true)
            ->where('rating', '>=', 4)
            ->with('user:id,name,avatar')
            ->latest()
            ->limit(6)
            ->get();

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

        ContactMessage::create($request->only('name', 'email', 'phone', 'subject', 'message'));

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

        NewsletterSubscription::firstOrCreate(
            ['email' => $request->email],
            ['is_active' => true]
        );

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
