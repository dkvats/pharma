<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->restrictOnDelete();
            $table->string('grn_number')->unique();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->date('received_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('received_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};
