<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;

        $query = Vehicle::available()
            ->with('category')
            ->withCount(['reviews as review_count' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->with('firstImage');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }
        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }
        if ($fuel = $request->get('fuel_type')) {
            $query->where('fuel_type', $fuel);
        }
        if ($trans = $request->get('transmission')) {
            $query->where('transmission', $trans);
        }
        if ($min = $request->get('min_price')) {
            $query->where('price_per_day', '>=', $min);
        }
        if ($max = $request->get('max_price')) {
            $query->where('price_per_day', '<=', $max);
        }

        $query->orderBy(match ($request->get('sort', 'popular')) {
            'price_low'  => 'price_per_day',
            'price_high' => 'price_per_day',
            'newest'     => 'created_at',
            default      => 'total_rentals',
        }, $request->get('sort', 'popular') === 'price_high' ? 'desc' : ($request->get('sort', 'popular') === 'newest' ? 'desc' : 'desc'));

        $vehicles = $query->paginate($perPage);

        $categories = VehicleCategory::where('is_active', true)
            ->withCount('vehicles')
            ->orderBy('sort_order')
            ->get();

        $maxPrice = Vehicle::available()->max('price_per_day') ?? 1000;

        return view('home.vehicles', compact('vehicles', 'categories', 'maxPrice'));
    }

    public function show(string $slug)
    {
        $vehicle = Vehicle::where('slug', $slug)
            ->with('category')
            ->withCount(['reviews as review_count' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->firstOrFail();

        $images = $vehicle->images()->orderBy('sort_order')->get();

        $reviews = $vehicle->reviews()
            ->where('is_approved', true)
            ->with('user:id,name,avatar')
            ->latest()
            ->limit(5)
            ->get();

        $reviewCount = $vehicle->reviews()->where('is_approved', true)->count();

        $relatedVehicles = Vehicle::available()
            ->where('category_id', $vehicle->category_id)
            ->where('id', '!=', $vehicle->id)
            ->withCount(['reviews as review_count' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->with('firstImage')
            ->limit(4)
            ->get();

        $userHasBookedVehicle = false;
        if (auth()->check()) {
            $userHasBookedVehicle = $vehicle->bookings()
                ->where('user_id', auth()->id())
                ->where('booking_status', 'completed')
                ->exists();
        }

        return view('home.vehicle-detail', compact('vehicle', 'images', 'reviews', 'reviewCount', 'relatedVehicles', 'userHasBookedVehicle'));
    }

    public function compare(Request $request)
    {
        $ids = array_filter(array_slice((array) $request->get('ids', []), 0, 4));

        $vehicles = [];
        if (!empty($ids)) {
            $vehicles = Vehicle::whereIn('id', $ids)
                ->with('category')
                ->withCount(['reviews as review_count' => function ($q) {
                    $q->where('is_approved', true);
                }])
                ->with('firstImage')
                ->get();
        }

        return view('home.compare', compact('vehicles'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $rows = Vehicle::available()
            ->where(function ($q) use ($query) {
                $q->where('vehicle_name', 'like', "%{$query}%")
                    ->orWhere('brand', 'like', "%{$query}%")
                    ->orWhere('model', 'like', "%{$query}%");
            })
            ->with('firstImage')
            ->select('id', 'vehicle_name', 'brand', 'price_per_day', 'slug')
            ->limit(8)
            ->get();

        $results = $rows->map(fn($v) => [
            'id'    => $v->id,
            'name'  => $v->vehicle_name,
            'brand' => $v->brand,
            'price' => $v->price_per_day,
            'image' => $v->primary_image ? asset('storage/' . $v->primary_image) : asset('images/vehicle-default.jpg'),
            'url'   => route('vehicles.show', $v->slug),
        ])->toArray();

        return response()->json($results);
    }
}
