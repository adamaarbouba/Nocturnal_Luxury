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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash'])->default('cash'); // Cash only for now
            $table->enum('type', ['payment', 'refund'])->default('payment'); // payment or refund
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->constrained('users')->onDelete('restrict');
            $table->dateTime('payment_date');
            $table->timestamps();

            $table->index(['booking_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
