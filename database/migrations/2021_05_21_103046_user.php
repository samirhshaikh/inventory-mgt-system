<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserTable extends Migration
{
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {

            $table->string('UserName',250);
            $table->string('Password',250)->nullable()->default('NULL');
            $table->tinyInteger('IsAdmin');
            $table->tinyInteger('IsActive');
            $table->datetime('CreatedDate')->nullable()->default('NULL');
            $table->string('CreatedBy',250)->nullable()->default('NULL');
            $table->datetime('UpdatedDate')->nullable()->default('NULL');
            $table->string('UpdatedBy',250)->nullable()->default('NULL');
            $table->primary('UserName');

            $table->foreign('CreatedBy')->references('UserName')->on('user');
            $table->foreign('UpdatedBy')->references('UserName')->on('user');

        });
    }

    public function down()
    {
        Schema::dropIfExists('user');
    }
}
