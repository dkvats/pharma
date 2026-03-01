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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('referred_by')->nullable()->constrained('users')->after('doctor_id');
            $table->index('referred_by', 'idx_orders_referred_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_referred_by');
            $table->dropForeign(['referred_by']);
            $table->dropColumn('referred_by');
        });
    }
};
