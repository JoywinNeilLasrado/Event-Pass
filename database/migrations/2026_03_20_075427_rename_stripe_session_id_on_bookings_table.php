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
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('stripe_session_id', 'cashfree_order_id');
        });
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'stripe_session_id')) {
                $table->renameColumn('stripe_session_id', 'cashfree_order_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->renameColumn('cashfree_order_id', 'stripe_session_id');
        });
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'cashfree_order_id')) {
                $table->renameColumn('cashfree_order_id', 'stripe_session_id');
            }
        });
    }
};
