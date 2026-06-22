<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('vehicle_categories')->onDelete('cascade');
            $table->string('vehicle_name');
            $table->string('slug')->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year')->nullable();
            $table->string('registration_number')->unique();
            $table->text('description')->nullable();
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid', 'cng'])->default('petrol');
            $table->enum('transmission', ['manual', 'automatic', 'semi-automatic'])->default('manual');
            $table->integer('seating_capacity')->default(5);
            $table->decimal('price_per_day', 10, 2);
            $table->enum('status', ['available', 'rented', 'maintenance', 'inactive'])->default('available');
            $table->string('color')->nullable();
            $table->string('mileage')->nullable();
            $table->string('engine_cc')->nullable();
            $table->boolean('air_conditioning')->default(true);
            $table->boolean('gps')->default(false);
            $table->boolean('bluetooth')->default(false);
            $table->boolean('usb_charger')->default(false);
            $table->boolean('child_seat')->default(false);
            $table->integer('total_rentals')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
