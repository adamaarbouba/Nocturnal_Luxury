<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['cleaner', 'inspector', 'receptionist']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('message')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable(); // set by owner on approval
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            // Prevent duplicate pending applications
            $table->unique(['user_id', 'hotel_id', 'role', 'status'], 'unique_pending_application');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_applications');
    }
};
