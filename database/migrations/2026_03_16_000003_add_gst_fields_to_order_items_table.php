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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('gst_percent', 5, 2)->default(0)->after('price');
            $table->decimal('gst_amount', 10, 2)->default(0)->after('gst_percent');
            $table->decimal('total_with_gst', 10, 2)->default(0)->after('gst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['gst_percent', 'gst_amount', 'total_with_gst']);
        });
    }
};
