<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('is_active');
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->index('status');
            $table->index('brand');
            $table->index('price_per_day');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('booking_status');
            $table->index('pickup_date');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('payment_status');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index('is_approved');
            $table->index('rating');
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->index('status');
            $table->index('maintenance_date');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->index('is_read');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['brand']);
            $table->dropIndex(['price_per_day']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_status']);
            $table->dropIndex(['pickup_date']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['is_approved']);
            $table->dropIndex(['rating']);
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['maintenance_date']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropIndex(['is_read']);
        });
    }
};
