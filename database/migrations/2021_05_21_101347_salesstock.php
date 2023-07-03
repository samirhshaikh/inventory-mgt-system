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
        if (!Schema::hasTable("salesstock")) {
            Schema::create("salesstock", function (Blueprint $table) {
                $table->increments("Id");
                $table->integer("InvoiceId", 11);
                $table
                    ->string("IMEI", 50)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->integer("Qty", 11)
                    ->nullable()
                    ->default("NULL");
                $table->text("Description");
                $table
                    ->double("Cost")
                    ->nullable()
                    ->default("NULL");
                $table
                    ->double("Discount")
                    ->nullable()
                    ->default("NULL");
                $table->tinyInteger("Returned", 1)->default("0");
                $table
                    ->datetime("ReturnedDate")
                    ->nullable()
                    ->default("NULL");
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
                    ->foreign("InvoiceId")
                    ->references("Id")
                    ->on("purchase");
                $table
                    ->foreign("IMEI")
                    ->references("IMEI")
                    ->on("phonestock");
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
        if (Schema::hasTable("salesstock")) {
            Schema::dropIfExists("salesstock");
        }
    }
};
