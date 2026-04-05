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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->bigInteger('amount_cents');
            $table->string('description');
            $table->string('reference_id')->nullable(); // Can be a payment gateway ID or an Order ID
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['user_id', 'type']); // Fo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
