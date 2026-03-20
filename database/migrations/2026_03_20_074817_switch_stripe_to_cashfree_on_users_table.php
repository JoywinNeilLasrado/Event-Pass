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
            $table->dropColumn(['stripe_account_id', 'stripe_onboarding_completed']);
            $table->string('cashfree_vendor_id')->nullable()->after('has_unlimited_events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('cashfree_vendor_id');
            $table->string('stripe_account_id')->nullable();
            $table->boolean('stripe_onboarding_completed')->default(false);
        });
    }
};
