<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\VehicleController as AdminVehicle;
use App\Http\Controllers\Admin\CategoryController as AdminCategory;
use App\Http\Controllers\Admin\BookingController as AdminBooking;
use App\Http\Controllers\Admin\CustomerController as AdminCustomer;
use App\Http\Controllers\Admin\PaymentController as AdminPayment;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenance;
use App\Http\Controllers\Admin\ReviewController as AdminReview;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Customer\BookingController as CustomerBooking;
use App\Http\Controllers\Customer\WishlistController as CustomerWishlist;
use App\Http\Controllers\Customer\ProfileController as CustomerProfile;
use App\Http\Controllers\Customer\ReviewController as CustomerReview;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CustomerMiddleware;


// | Public Routes

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::post('/newsletter', [HomeController::class, 'newsletter'])->name('newsletter.subscribe');

/*
 Vehicle Routes (Public)
*/

Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
Route::get('/vehicles/compare', [VehicleController::class, 'compare'])->name('vehicles.compare');
Route::get('/vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
Route::get('/vehicles/{slug}', [VehicleController::class, 'show'])->name('vehicles.show');

// Auth Routes

Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Customer Routes (Protected)

Route::middleware(['auth', CustomerMiddleware::class])->prefix('customer')->name('customer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');

    // Bookings
    Route::get('/bookings', [CustomerBooking::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create/{slug}', [CustomerBooking::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [CustomerBooking::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{bookingId}', [CustomerBooking::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{bookingId}/cancel', [CustomerBooking::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{bookingId}/invoice', [CustomerBooking::class, 'invoice'])->name('bookings.invoice');

    // Wishlist
    Route::get('/wishlist', [CustomerWishlist::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [CustomerWishlist::class, 'toggle'])->name('wishlist.toggle');

    // Profile
    Route::get('/profile', [CustomerProfile::class, 'edit'])->name('profile');
    Route::match(['post', 'put'], '/profile', [CustomerProfile::class, 'update'])->name('profile.update');
    Route::post('/password', [CustomerProfile::class, 'changePassword'])->name('password.update');

    // Reviews
    Route::post('/reviews', [CustomerReview::class, 'store'])->name('reviews.store');
});

// Admin Routes (Protected)
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Vehicles
    Route::get('/vehicles', [AdminVehicle::class, 'index'])->name('vehicles.index');
    Route::get('/vehicles/create', [AdminVehicle::class, 'create'])->name('vehicles.create');
    Route::post('/vehicles', [AdminVehicle::class, 'store'])->name('vehicles.store');
    Route::get('/vehicles/{id}', [AdminVehicle::class, 'show'])->name('vehicles.show');
    Route::get('/vehicles/{id}/edit', [AdminVehicle::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicles/{id}', [AdminVehicle::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicles/{id}', [AdminVehicle::class, 'destroy'])->name('vehicles.destroy');

    // Categories
    Route::get('/categories', [AdminCategory::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategory::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategory::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [AdminCategory::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [AdminCategory::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategory::class, 'destroy'])->name('categories.destroy');

    // Bookings
    Route::get('/bookings', [AdminBooking::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [AdminBooking::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/approve', [AdminBooking::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{id}/reject', [AdminBooking::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{id}/complete', [AdminBooking::class, 'complete'])->name('bookings.complete');

    // Payments
    Route::get('/payments', [AdminPayment::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [AdminPayment::class, 'show'])->name('payments.show');
    Route::post('/payments/{id}/status', [AdminPayment::class, 'updateStatus'])->name('payments.status');

    // Customers
    Route::get('/customers', [AdminCustomer::class, 'index'])->name('customers.index');
    Route::get('/customers/{id}', [AdminCustomer::class, 'show'])->name('customers.show');
    Route::post('/customers/{id}/toggle', [AdminCustomer::class, 'toggleStatus'])->name('customers.toggle');

    // Maintenance
    Route::get('/maintenance', [AdminMaintenance::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [AdminMaintenance::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [AdminMaintenance::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{id}/edit', [AdminMaintenance::class, 'edit'])->name('maintenance.edit');
    Route::put('/maintenance/{id}', [AdminMaintenance::class, 'update'])->name('maintenance.update');
    Route::delete('/maintenance/{id}', [AdminMaintenance::class, 'destroy'])->name('maintenance.destroy');

    // Reviews
    Route::get('/reviews', [AdminReview::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/approve', [AdminReview::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{id}/reject', [AdminReview::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{id}', [AdminReview::class, 'destroy'])->name('reviews.destroy');

    // Reports
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [AdminReport::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/vehicles', [AdminReport::class, 'vehicles'])->name('reports.vehicles');
    Route::get('/reports/customers', [AdminReport::class, 'customers'])->name('reports.customers');
    Route::get('/reports/maintenance', [AdminReport::class, 'maintenance'])->name('reports.maintenance');
});
