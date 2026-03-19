<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->decimal('discount_amount', 10, 2);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('percentage');
            $table->integer('max_uses')->nullable();
            $table->integer('uses')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['event_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
