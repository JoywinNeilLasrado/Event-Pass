<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('id');
            $table->string('payment_status')->default('free')->after('is_featured');
            $table->string('stripe_session_id')->nullable()->after('payment_status');
        });

        // Ensure all existing events are visible by default
        DB::table('events')->update(['is_published' => true, 'payment_status' => 'paid']);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'payment_status', 'stripe_session_id']);
        });
    }
};
