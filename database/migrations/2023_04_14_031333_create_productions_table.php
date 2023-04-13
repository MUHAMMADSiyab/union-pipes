<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('null');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('shift');
            $table->decimal('weight', 8, 2)->default(0);
            $table->decimal('quantity', 8, 2)->default(0);
            $table->decimal('total_weight', 8, 2)->default(0);
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
        Schema::dropIfExists('productions');
    }
}
