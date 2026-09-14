<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // e.g. Toyota Innova Crysta
            $table->string('type');                   // Car, Bike
            $table->string('fuel_type');               // Petrol, Diesel
            $table->string('transmission');            // Manual, Automatic
            $table->decimal('price_per_day', 10, 2);
            $table->integer('seats')->nullable();
            $table->integer('model_year')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();      // ["AC","Power Steering",...]
            $table->string('image')->nullable();        // main image path
            $table->json('gallery')->nullable();         // extra images
            $table->enum('status', ['available', 'maintenance', 'unavailable'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
