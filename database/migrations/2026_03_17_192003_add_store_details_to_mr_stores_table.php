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
        Schema::table('mr_stores', function (Blueprint $table) {
            // A. Basic Store Details
            $table->string('alt_phone')->nullable()->after('phone');
            
            // B. Owner Identity
            $table->string('aadhaar')->nullable()->unique()->after('email');
            $table->string('owner_photo')->nullable()->after('aadhaar');
            
            // C. Store Media
            $table->string('store_photo')->nullable()->after('owner_photo');
            
            // E. Business Details
            $table->string('gst_no')->nullable()->unique()->after('address');
            $table->string('drug_license_no')->nullable()->unique()->after('gst_no');
            $table->date('license_expiry')->nullable()->after('drug_license_no');
            $table->string('pan_no')->nullable()->unique()->after('license_expiry');
            
            // F. Store Type
            $table->enum('store_type', ['retail', 'distributor', 'clinic', 'pharmacy'])->nullable()->after('pan_no');
            
            // G. Financial Settings
            $table->decimal('default_discount', 5, 2)->default(0)->after('store_type');
            $table->decimal('credit_limit', 12, 2)->default(0)->after('default_discount');
            $table->string('payment_terms')->nullable()->after('credit_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mr_stores', function (Blueprint $table) {
            $table->dropColumn([
                'alt_phone',
                'aadhaar',
                'owner_photo',
                'store_photo',
                'gst_no',
                'drug_license_no',
                'license_expiry',
                'pan_no',
                'store_type',
                'default_discount',
                'credit_limit',
                'payment_terms',
            ]);
        });
    }
};
