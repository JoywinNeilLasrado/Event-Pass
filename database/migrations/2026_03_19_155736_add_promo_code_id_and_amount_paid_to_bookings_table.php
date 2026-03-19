<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->decimal('amount_paid', 10, 2)->default(0)->after('ticket_type_id');
        });

        // Ensure historical bookings have an amount_paid mirroring their ticket tier so analytics remain robust
        DB::statement('UPDATE bookings b JOIN ticket_types t ON b.ticket_type_id = t.id SET b.amount_paid = t.price');
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['promo_code_id']);
            $table->dropColumn(['promo_code_id', 'amount_paid']);
        });
    }
};
