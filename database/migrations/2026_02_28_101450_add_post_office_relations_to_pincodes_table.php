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
        Schema::table('pincodes', function (Blueprint $table) {
            $table->string('office_type')->nullable()->after('post_office');
            $table->string('related_suboffice')->nullable()->after('office_type');
            $table->string('related_headoffice')->nullable()->after('related_suboffice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pincodes', function (Blueprint $table) {
            $table->dropColumn(['office_type', 'related_suboffice', 'related_headoffice']);
        });
    }
};
