<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawMaterialEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_material_entries', function (Blueprint $table) {
            $table->id();
            $table->string('quality');
            $table->integer('bag')->default(0);
            $table->integer('kg')->default(0);
            $table->integer('weight')->default(0);
            $table->integer('rate')->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('raw_material_entries');
    }
}
