<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->date('month');
            $table->date('date');
            $table->float('total_paid', 15, 2)->default(0);
            $table->float('additional_amount', 15, 2)->default(0);
            $table->float('deducted_amount', 15, 2)->default(0);
            $table->string('status')->default('unpaid');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
