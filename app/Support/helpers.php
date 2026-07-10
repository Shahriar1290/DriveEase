<?php

/**
 * Global view helpers
 * ---------------------------------------------------------------------
 * Since every controller in this app now returns raw stdClass rows from
 * hand-written SQL (no Eloquent models, no accessors like
 * ->status_badge or ->primary_image_url), these plain functions
 * replace those old Eloquent "get...Attribute()" accessors so Blade
 * views can keep using simple, readable calls.
 * ---------------------------------------------------------------------
 */

if (!function_exists('booking_status_badge')) {
    function booking_status_badge(string $status): string
    {
        return match ($status) {
            'pending'   => '<span class="badge bg-warning">Pending</span>',
            'approved'  => '<span class="badge bg-success">Approved</span>',
            'active'    => '<span class="badge bg-primary">Active</span>',
            'completed' => '<span class="badge bg-info">Completed</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
            'rejected'  => '<span class="badge bg-danger">Rejected</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($status) . '</span>',
        };
    }
}

if (!function_exists('payment_status_badge')) {
    function payment_status_badge(string $status): string
    {
        return match ($status) {
            'completed' => '<span class="badge bg-success">Completed</span>',
            'pending'   => '<span class="badge bg-warning">Pending</span>',
            'failed'    => '<span class="badge bg-danger">Failed</span>',
            'refunded'  => '<span class="badge bg-info">Refunded</span>',
            default     => '<span class="badge bg-secondary">' . ucfirst($status) . '</span>',
        };
    }
}

if (!function_exists('maintenance_status_badge')) {
    function maintenance_status_badge(string $status): string
    {
        return match ($status) {
            'completed'   => '<span class="badge bg-success">Completed</span>',
            'in_progress' => '<span class="badge bg-primary">In Progress</span>',
            'scheduled'   => '<span class="badge bg-warning">Scheduled</span>',
            default       => '<span class="badge bg-secondary">' . ucfirst($status) . '</span>',
        };
    }
}

if (!function_exists('vehicle_image_url')) {
    function vehicle_image_url(?string $path): string
    {
        if ($path && file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="200" fill="#e2e8f0"><rect width="400" height="200" rx="8"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-size="18" font-family="sans-serif">No Image Available</text></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

if (!function_exists('category_image_url')) {
    function category_image_url(?string $path): string
    {
        if ($path && file_exists(public_path('storage/' . $path))) {
            return asset('storage/' . $path);
        }
        return 'data:image/svg+xml,' . urlencode('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="#e2e8f0"><rect width="100" height="100" rx="8"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-size="14" font-family="sans-serif">No Image</text></svg>');
    }
}

if (!function_exists('user_avatar_url')) {
    function user_avatar_url(?string $path, string $name = ''): string
    {
        if ($path) {
            return asset('storage/' . $path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=0ea5e9&color=fff&size=128';
    }
}

if (!function_exists('star_rating_html')) {
    function star_rating_html(int $rating, int $max = 5): string
    {
        $html = '';
        for ($i = 1; $i <= $max; $i++) {
            $html .= $i <= $rating
                ? '<i class="fas fa-star text-warning"></i>'
                : '<i class="far fa-star text-muted"></i>';
        }
        return $html;
    }
}
