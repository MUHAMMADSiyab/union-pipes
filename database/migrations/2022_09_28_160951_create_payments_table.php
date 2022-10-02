<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
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
            $table->float('amount', 15, 2);
            $table->string('transaction_type');
            $table->string('model');
            $table->bigInteger('paymentable_id');
            $table->string('payment_method');
            $table->string('cheque_no')->nullable();
            $table->string('cheque_type')->nullable();
            $table->date('cheque_due_date')->nullable();
            $table->dateTime('payment_date');
            $table->foreignId('bank_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
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
}
