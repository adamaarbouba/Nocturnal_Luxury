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
        Schema::dropIfExists('rooms');

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('room_number');
            $table->string('room_type'); // Single, Double, Suite, Deluxe, etc.
            $table->integer('floor')->nullable();
            $table->integer('capacity');
            $table->decimal('price_per_night', 8, 2);
            $table->enum('status', ['Available', 'Reserved', 'Occupied', 'Cleaning', 'Inspection', 'Maintenance', 'Disabled'])->default('Available');
            $table->timestamps();

            $table->unique(['hotel_id', 'room_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
