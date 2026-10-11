<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user:id,name', 'vehicle:id,vehicle_name']);

        if ($request->status === 'approved') {
            $query->where('is_approved', true);
        }
        if ($request->status === 'pending') {
            $query->where('is_approved', false);
        }
        if ($s = $request->search) {
            $query->whereHas('vehicle', fn($q) => $q->where('vehicle_name', 'like', "%{$s}%"));
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(int $id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);
        $this->recalculateVehicleRating($review->vehicle_id);

        return back()->with('success', 'Review approved.');
    }

    public function reject(int $id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => false]);
        $this->recalculateVehicleRating($review->vehicle_id);

        return back()->with('success', 'Review rejected.');
    }

    public function destroy(int $id)
    {
        $review = Review::findOrFail($id);
        $vehicleId = $review->vehicle_id;
        $review->delete();
        $this->recalculateVehicleRating($vehicleId);

        return back()->with('success', 'Review deleted.');
    }

    private function recalculateVehicleRating(int $vehicleId): void
    {
        $avg = Review::where('vehicle_id', $vehicleId)
            ->where('is_approved', true)
            ->avg('rating');

        Vehicle::where('id', $vehicleId)->update(['average_rating' => round($avg, 2)]);
    }
}
