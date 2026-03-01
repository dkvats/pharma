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
        Schema::table('mr_doctors', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'inactive'])
                ->default('pending')
                ->after('is_active');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
        });
        
        // Migrate existing data: approved doctors keep approved status, others become pending
        DB::statement("UPDATE mr_doctors SET status = 'approved' WHERE is_active = 1 AND user_id IS NOT NULL");
        DB::statement("UPDATE mr_doctors SET status = 'pending' WHERE status IS NULL OR status = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mr_doctors', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['status', 'rejection_reason', 'approved_at', 'approved_by']);
        });
    }
};
