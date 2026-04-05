<?php

use App\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->float('amount')->comment('Payment amount in USD');
            $table->string('currency');
            $table->string('transaction_id')->unique();
            $table->boolean('has_used')->default(false);
            $table->string('status')->default(Payment::WAITING_STATUS); // check different status based on payment gateways
            $table->string('paid_amount')->comment('Amount paid in the original currency'); // e.g., BTC, ETH
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
