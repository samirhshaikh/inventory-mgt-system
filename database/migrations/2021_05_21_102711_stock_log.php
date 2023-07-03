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
        if (!Schema::hasTable("stock_log")) {
            Schema::create("stock_log", function (Blueprint $table) {
                $table->increments("Id");
                $table->string("IMEI", 50);
                $table->datetime("LogDate");
                $table->text("Comments");
                $table->string("Activity", 45)->default("Sold");
                $table->datetime("CreatedDate");
                $table->string("CreatedBy", 250);
                $table->datetime("UpdatedDate");
                $table->string("UpdatedBy", 250);
                $table->primary("Id");

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
        if (Schema::hasTable("stock_log")) {
            Schema::dropIfExists("stock_log");
        }
    }
};
