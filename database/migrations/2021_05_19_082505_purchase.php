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
        if (!Schema::hasTable("purchase")) {
            Schema::create("purchase", function (Blueprint $table) {
                $table->increments("Id");
                $table->string("InvoiceNo", 50)->nullable();
                $table->datetime("InvoiceDate");
                $table->integer("SupplierId", 11)->nullable();
                $table->text("Comments");
                $table->tinyInteger("IsActive")->default(0);
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();
                $table->integer("ParentId")->nullable();

                $table->primary("Id");

                $table
                    ->foreign("SupplierId")
                    ->references("Id")
                    ->on("supplier");
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
        if (Schema::hasTable("purchase")) {
            Schema::dropIfExists("purchase");
        }
    }
};
