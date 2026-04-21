<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL doesn't support easy column modification for ENUMs in some versions via Blueprint
        // We use a raw statement to ensure compatibility
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'credit_card') DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash') DEFAULT 'cash'");
    }
};
