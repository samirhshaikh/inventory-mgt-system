<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTable extends Migration
{
    public function up()
    {
        Schema::create('purchase', function (Blueprint $table) {

            $table->increments(Id);
            $table->string('InvoiceNo',50)->nullable()->default('NULL');
            $table->datetime('InvoiceDate');
            $table->integer('SupplierId',11)->nullable()->default('NULL');
            $table->text('Comments');
            $table->tinyInteger('IsActive',4);
            $table->datetime('CreatedDate');
            $table->string('CreatedBy',20);
            $table->datetime('UpdatedDate')->nullable()->default('NULL');
            $table->string('UpdatedBy',20)->nullable()->default('NULL');
            $table->primary('Id');

            $table->foreign('SupplierId')->references('Id')->on('supplier');
            $table->foreign('CreatedBy')->references('UserName')->on('user');
            $table->foreign('UpdatedBy')->references('UserName')->on('user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase');
    }
}
