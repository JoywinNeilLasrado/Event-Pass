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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default fees
        \Illuminate\Support\Facades\DB::table('settings')->insert([
            ['key' => 'organizer_fee', 'value' => '500', 'description' => 'One-time fee to become an organizer (INR)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'event_fee', 'value' => '100', 'description' => 'Fee to publish a new event (INR)', 'created_at' => now(), 'updated_at' => now()]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
