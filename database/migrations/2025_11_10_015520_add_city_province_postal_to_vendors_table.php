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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('business_city', 100)->nullable()->after('business_address');
            $table->string('business_province', 100)->nullable()->after('business_city');
            $table->string('business_postal_code', 10)->nullable()->after('business_province');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['business_city', 'business_province', 'business_postal_code']);
        });
    }
};
