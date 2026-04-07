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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained('orders', 'id')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('external_order_id')->index(); // ID from the central server
            $table->string('service_name');
            $table->string('phone_number');
            $table->unsignedBigInteger('price_cents'); // Price for this individual number
            $table->enum('status', ['pending', 'active', 'completed', 'timeout_refunded'])->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'service_name']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
