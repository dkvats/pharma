<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('batch_no', 120);
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date');
            $table->integer('quantity')->default(0);
            $table->integer('free_quantity')->default(0);
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('mrp', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('gst', 6, 2)->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_no')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('location')->nullable();
            $table->integer('min_stock')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
