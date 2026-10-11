<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with('category')
            ->withCount('bookings')
            ->with('firstImage');

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('vehicle_name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }
        if ($cat = $request->category_id) {
            $query->where('category_id', $cat);
        }
        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();
        $categories = VehicleCategory::orderBy('category_name')->get();

        return view('admin.vehicles.index', compact('vehicles', 'categories'));
    }

    public function create()
    {
        $categories = VehicleCategory::orderBy('category_name')->get();
        return view('admin.vehicles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'         => 'required|integer',
            'vehicle_name'        => 'required|string|max:255',
            'brand'                => 'required|string|max:100',
            'model'                => 'required|string|max:100',
            'year'                 => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'registration_number'  => 'required|string',
            'description'          => 'nullable|string',
            'fuel_type'            => 'required|in:petrol,diesel,electric,hybrid,cng',
            'transmission'         => 'required|in:manual,automatic,semi-automatic',
            'seating_capacity'     => 'required|integer|min:1|max:50',
            'price_per_day'        => 'required|numeric|min:0',
            'status'               => 'required|in:available,rented,maintenance,inactive',
            'color'                => 'nullable|string|max:50',
            'mileage'              => 'nullable|string|max:50',
            'engine_cc'            => 'nullable|string|max:50',
            'images.*'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if (Vehicle::where('registration_number', $data['registration_number'])->exists()) {
            return back()->withErrors(['registration_number' => 'This registration number is already in use.'])->withInput();
        }

        $vehicle = Vehicle::create(array_merge($data, [
            'slug' => Str::slug($data['vehicle_name'] . '-' . $data['registration_number']),
            'air_conditioning' => $request->boolean('air_conditioning'),
            'gps' => $request->boolean('gps'),
            'bluetooth' => $request->boolean('bluetooth'),
            'usb_charger' => $request->boolean('usb_charger'),
            'child_seat' => $request->boolean('child_seat'),
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $vehicle->images()->create([
                    'image' => $file->store('vehicles', 'public'),
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle added successfully.');
    }

    public function show(int $id)
    {
        $vehicle = Vehicle::with('category')->findOrFail($id);

        $images = $vehicle->images()->orderBy('sort_order')->get();

        $bookings = $vehicle->bookings()
            ->with('user:id,name')
            ->latest()
            ->limit(8)
            ->get();

        $maintenanceRecords = $vehicle->maintenanceRecords()
            ->orderByDesc('maintenance_date')
            ->get();

        $reviews = $vehicle->reviews()
            ->with('user:id,name')
            ->latest()
            ->limit(5)
            ->get();

        $approvedReviewCount = $vehicle->reviews()->where('is_approved', true)->count();
        $allReviewCount = $vehicle->reviews()->count();

        return view('admin.vehicles.show', compact(
            'vehicle', 'images', 'bookings', 'maintenanceRecords', 'reviews', 'approvedReviewCount', 'allReviewCount'
        ));
    }

    public function edit(int $id)
    {
        $vehicle = Vehicle::with('images')->findOrFail($id);
        $categories = VehicleCategory::orderBy('category_name')->get();

        return view('admin.vehicles.create', compact('vehicle', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        $data = $request->validate([
            'category_id'         => 'required|integer',
            'vehicle_name'        => 'required|string|max:255',
            'brand'                => 'required|string|max:100',
            'model'                => 'required|string|max:100',
            'year'                 => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'registration_number'  => 'required|string',
            'description'          => 'nullable|string',
            'fuel_type'            => 'required|in:petrol,diesel,electric,hybrid,cng',
            'transmission'         => 'required|in:manual,automatic,semi-automatic',
            'seating_capacity'     => 'required|integer|min:1|max:50',
            'price_per_day'        => 'required|numeric|min:0',
            'status'               => 'required|in:available,rented,maintenance,inactive',
            'color'                => 'nullable|string|max:50',
            'mileage'              => 'nullable|string|max:50',
            'engine_cc'            => 'nullable|string|max:50',
            'new_images.*'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if (Vehicle::where('registration_number', $data['registration_number'])->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['registration_number' => 'This registration number is already in use.'])->withInput();
        }

        $vehicle->update(array_merge($data, [
            'air_conditioning' => $request->boolean('air_conditioning'),
            'gps' => $request->boolean('gps'),
            'bluetooth' => $request->boolean('bluetooth'),
            'usb_charger' => $request->boolean('usb_charger'),
            'child_seat' => $request->boolean('child_seat'),
        ]));

        if ($request->delete_images) {
            foreach ($request->delete_images as $imgId) {
                $img = VehicleImage::where('id', $imgId)->where('vehicle_id', $id)->first();
                if ($img) {
                    Storage::disk('public')->delete($img->image);
                    $img->delete();
                }
            }
        }

        if ($request->hasFile('new_images')) {
            $existingCount = $vehicle->images()->count();
            foreach ($request->file('new_images') as $index => $file) {
                $vehicle->images()->create([
                    'image' => $file->store('vehicles', 'public'),
                    'is_primary' => $existingCount === 0 && $index === 0,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(int $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if ($vehicle->bookings()->exists()) {
            return back()->with('error', 'Cannot delete this vehicle because it has existing booking history. Set its status to "inactive" instead.');
        }

        foreach ($vehicle->images as $img) {
            Storage::disk('public')->delete($img->image);
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
