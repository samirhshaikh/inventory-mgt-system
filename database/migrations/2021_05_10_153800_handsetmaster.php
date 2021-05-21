<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHandsetmasterTable extends Migration
{
    public function up()
    {
        Schema::create('handsetmaster', function (Blueprint $table) {

            $table->increments(Id);
            $table->string('Name')->nullable()->default('NULL');
            $table->integer('MakeId',11)->nullable()->default('NULL');
            $table->integer('ModelId',11)->nullable()->default('NULL');
            $table->integer('ColorId',11)->nullable()->default('NULL');
            $table->tinyInteger('IsActive',1);
            $table->datetime('CreatedDate')->nullable()->default('NULL');
            $table->string('CreatedBy',250)->nullable()->default('NULL');
            $table->datetime('UpdatedDate')->nullable()->default('NULL');
            $table->string('UpdatedBy',250)->nullable()->default('NULL');
            $table->primary('Id');

            $table->foreign('CreatedBy')->references('UserName')->on('user');
            $table->foreign('UpdatedBy')->references('UserName')->on('user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('handsetmaster');
    }
}
