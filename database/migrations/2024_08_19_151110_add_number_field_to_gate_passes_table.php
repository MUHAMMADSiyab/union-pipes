<?php

use App\Models\GatePass;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumberFieldToGatePassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->bigInteger('number')->default(0)->before('created_at');
        });

        $number = 0;
        foreach (GatePass::all() as $gate_pass) {
            $number++;
            $gate_pass->update(['number' => $number]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gate_passes', function (Blueprint $table) {
            $table->dropColumn('number');
        });
    }
}
