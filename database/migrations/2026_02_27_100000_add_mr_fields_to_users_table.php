<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // MR-specific fields - all nullable, won't affect other roles
            $table->string('employee_code')->nullable()->after('unique_code');
            $table->string('designation')->nullable()->after('employee_code');
            $table->string('assigned_area')->nullable()->after('designation');
            
            // Add index for employee_code lookups
            $table->index('employee_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['employee_code']);
            $table->dropColumn(['employee_code', 'designation', 'assigned_area']);
        });
    }
};
