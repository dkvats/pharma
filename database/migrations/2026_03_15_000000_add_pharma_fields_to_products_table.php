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
        Schema::table('products', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('category');
            $table->string('company')->nullable()->after('brand');
            $table->string('batch_number')->nullable()->after('stock');
            $table->date('expiry_date')->nullable()->after('batch_number');
            $table->decimal('gst_percent', 5, 2)->default(0)->after('discount_amount');
            $table->string('sku')->unique()->nullable()->after('company');
            $table->string('unit_type')->nullable()->after('sku');
            $table->integer('low_stock_alert')->default(10)->after('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'company',
                'batch_number',
                'expiry_date',
                'gst_percent',
                'sku',
                'unit_type',
                'low_stock_alert'
            ]);
        });
    }
};
