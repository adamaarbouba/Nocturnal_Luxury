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
        Schema::create('hotel_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->enum('role', ['cleaner', 'inspector']);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->time('available_from')->nullable();
            $table->time('available_to')->nullable();
            $table->json('available_days')->nullable(); // ["Monday", "Tuesday", ...]
            $table->timestamps();

            $table->unique(['user_id', 'hotel_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_staff');
    }
};
