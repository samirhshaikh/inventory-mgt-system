<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable("customer")) {
            Schema::create("customer", function (Blueprint $table) {
                $table->increments("Id");
                $table
                    ->string("CustomerName", 350)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->string("ContactNo1", 20)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->string("ContactNo2", 20)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->string("ContactNo3", 20)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->string("City", 100)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->double("Balance")
                    ->nullable()
                    ->default("NULL");
                $table->text("Comments");
                $table->tinyInteger("IsActive");
                $table
                    ->datetime("CreatedDate")
                    ->nullable()
                    ->default("NULL");
                $table
                    ->string("CreatedBy", 250)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->datetime("UpdatedDate")
                    ->nullable()
                    ->default("NULL");
                $table
                    ->string("UpdatedBy", 250)
                    ->nullable()
                    ->default("NULL");
                $table->primary("Id");

                $table
                    ->foreign("CreatedBy")
                    ->references("UserName")
                    ->on("user");
                $table
                    ->foreign("UpdatedBy")
                    ->references("UserName")
                    ->on("user");
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable("customer")) {
            Schema::dropIfExists("customer");
        }
    }
};
