<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {

            $table->increments(Id);
            $table->integer('CustomerId',11)->nullable()->default('NULL');
            $table->integer('RepairId',11)->nullable()->default('NULL');
            $table->string('InvoiceNo',20)->nullable()->default('NULL');
            $table->datetime('InvoiceDate')->nullable()->default('NULL');
            $table->tinyInteger('BusinessInvoice',4)->default('0');
            $table->string('PaymentMethod',200)->nullable()->default('NULL');
            $table->string('ChequeNo',50)->nullable()->default('NULL');
            $table->text('Comments');
            $table->double('VAT')->nullable()->default('NULL');
            $table->tinyInteger('IsActive',1);
            $table->datetime('CreatedDate')->nullable()->default('NULL');
            $table->string('CreatedBy',250)->nullable()->default('NULL');
            $table->datetime('UpdatedDate')->nullable()->default('NULL');
            $table->string('UpdatedBy',250)->nullable()->default('NULL');
            $table->text('AccessoriesDesc');
            $table->double('AccessoriesAmount')->nullable()->default('NULL');
            $table->primary('Id');

            $table->foreign('CustomerId')->references('Id')->on('customer_sales');
            $table->foreign('CreatedBy')->references('UserName')->on('user');
            $table->foreign('UpdatedBy')->references('UserName')->on('user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
