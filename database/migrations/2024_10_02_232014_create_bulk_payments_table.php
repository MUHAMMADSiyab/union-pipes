<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBulkPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bulk_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->dateTime('date');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('payment_method');
            $table->string('cheque_no')->nullable();
            $table->string('cheque_type')->nullable();
            $table->date('cheque_due_date')->nullable();
            $table->foreignId('bank_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('bulk_payments');
    }
}
