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
        if (!Schema::hasTable("manufacturemaster")) {
            Schema::create("manufacturemaster", function (Blueprint $table) {
                $table->increments("Id");
                $table
                    ->string("Name")
                    ->nullable()
                    ->default("NULL");
                $table->tinyInteger("IsActive", 1);
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
        if (Schema::hasTable("manufacturemaster")) {
            Schema::dropIfExists("manufacturemaster");
        }
    }
};
