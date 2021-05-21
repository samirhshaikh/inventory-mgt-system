<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhoneStockTable extends Migration
{
    public function up()
    {
        Schema::create('phonestock', function (Blueprint $table) {

            $table->increments(Id);
            $table->integer('InvoiceId',11)->nullable()->default('NULL');
            $table->integer('MakeId',11)->nullable()->default('NULL');
            $table->integer('ModelId',11)->nullable()->default('NULL');
            $table->integer('ColorId',11)->nullable()->default('NULL');
            $table->string('Size',50)->nullable()->default('NULL');
            $table->string('IMEI',50)->nullable()->default('NULL');
            $table->double('Cost')->nullable()->default('NULL');
            $table->string('StockType',15)->nullable()->default('NULL');
            $table->string('ModelNo',30)->nullable()->default('NULL');
            $table->string('Network',50)->nullable()->default('NULL');
            $table->string('Status',20)->nullable()->default('NULL');
            $table->tinyInteger('IsActive',1);
            $table->datetime('CreatedDate')->nullable()->default('NULL');
            $table->string('CreatedBy',250)->nullable()->default('NULL');
            $table->datetime('UpdatedDate')->nullable()->default('NULL');
            $table->string('UpdatedBy',250)->nullable()->default('NULL');
            $table->integer('ParentId',11)->nullable()->default('NULL');
            $table->primary('Id');

            $table->foreign('InvoiceId')->references('Id')->on('purchase');
            $table->foreign('MakeId')->references('Id')->on('manufacturermaster');
            $table->foreign('ModelId')->references('Id')->on('modelmaster');
            $table->foreign('ColorId')->references('Id')->on('colormaster');
            $table->foreign('CreatedBy')->references('UserName')->on('user');
            $table->foreign('UpdatedBy')->references('UserName')->on('user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('phonestock');
    }
}
