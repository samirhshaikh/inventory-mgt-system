<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockLogTable extends Migration
{
    public function up()
    {
        Schema::create('stock_log', function (Blueprint $table) {

            $table->increments(Id);
            $table->string('IMEI',50);
            $table->datetime('LogDate');
            $table->text('Comments');
            $table->string('Activity',45)->default('Sold');
            $table->datetime('CreatedDate');
            $table->string('CreatedBy',250);
            $table->datetime('UpdatedDate');
            $table->string('UpdatedBy',250);
            $table->primary('Id');

            $table->foreign('IMEI')->references('IMEI')->on('phonestock');
            $table->foreign('CreatedBy')->references('UserName')->on('user');
            $table->foreign('UpdatedBy')->references('UserName')->on('user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_log');
    }
}
