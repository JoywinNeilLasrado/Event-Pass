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
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status')->nullable()->after('is_organizer');
            $table->text('business_details')->nullable()->after('kyc_status');
            $table->text('social_links')->nullable()->after('business_details');
        });

        // Grandfather existing organizers natively protecting event accessibility
        \Illuminate\Support\Facades\DB::statement("UPDATE users SET kyc_status = 'approved' WHERE is_organizer = 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kyc_status', 'business_details', 'social_links']);
        });
    }
};
