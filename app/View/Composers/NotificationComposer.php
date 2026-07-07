<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * NotificationComposer
 * ---------------------------------------------------------------------
 * The Blade layouts (app, admin, customer) all need the logged-in
 * user's unread notification count and recent notification list in
 * the navbar/topbar. Previously this was done with inline Eloquent
 * calls like auth()->user()->unreadNotifications()->count() directly
 * inside the Blade file.
 *
 * Since the User model no longer has any relationships (raw SQL only),
 * this composer runs two small raw SQL queries once per request and
 * shares the results with the layout views as plain variables.
 * ---------------------------------------------------------------------
 */
class NotificationComposer
{
    public function compose(View $view): void
    {
        $unreadCount   = 0;
        $notifications = [];

        if (auth()->check()) {
            $userId = auth()->id();

            $unreadCount = DB::select(
                "SELECT COUNT(*) AS total FROM notifications WHERE user_id = ? AND status = 'unread'",
                [$userId]
            )[0]->total;

            $notifications = DB::select(
                "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5",
                [$userId]
            );
        }

        $view->with('navUnreadCount', $unreadCount);
        $view->with('navNotifications', $notifications);

        // Admin sidebar badge counts — only meaningful when an admin is logged in,
        // but cheap enough to compute unconditionally for any layout that needs them.
        if (auth()->check() && auth()->user()->isAdmin()) {
            $view->with('sidebarPendingBookings', DB::select(
                "SELECT COUNT(*) AS total FROM bookings WHERE booking_status = 'pending'"
            )[0]->total);

            $view->with('sidebarPendingReviews', DB::select(
                "SELECT COUNT(*) AS total FROM reviews WHERE is_approved = 0"
            )[0]->total);
        } else {
            $view->with('sidebarPendingBookings', 0);
            $view->with('sidebarPendingReviews', 0);
        }
    }
}
