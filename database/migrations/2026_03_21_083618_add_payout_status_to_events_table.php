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
        Schema::table('events', function (Blueprint $table) {
            $table->string('payout_status')->default('pending')->after('payment_status');
            $table->decimal('payout_amount', 10, 2)->default(0)->after('payout_status');
            $table->string('payout_reference_id')->nullable()->after('payout_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['payout_status', 'payout_amount', 'payout_reference_id']);
        });
    }
};
