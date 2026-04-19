<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            // صاحب الطلب
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // حالة الطلب
            $table->enum('status', ['pending', 'purchased', 'delivered'])
                  ->default('pending');

            $table->decimal('total_estimated_price', 10, 2)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};