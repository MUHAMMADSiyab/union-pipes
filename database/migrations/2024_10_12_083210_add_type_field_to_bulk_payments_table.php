<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeFieldToBulkPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bulk_payments', function (Blueprint $table) {
            $table->after('amount', function () use ($table) {
                $table->string('type');
                $table->foreignId('customer_id')->nullable()->change();
                $table->foreignId('company_id')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bulk_payments', function (Blueprint $table) {
            $table->dropColumn(['type', 'company_id']);
            $table->foreignId('customer_id')->change();
        });
    }
}
