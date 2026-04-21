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
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('status', ['pending_approval', 'approved', 'rejected'])->default('pending_approval');
            $table->decimal('rating', 3, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropForeignIdFor('users');
            $table->dropColumn(['description', 'phone', 'email', 'city', 'country', 'postal_code', 'status', 'rating']);
        });
    }
};
