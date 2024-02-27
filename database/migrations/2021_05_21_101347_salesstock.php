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
                $table->id();
                $table->integer("InvoiceId");
                $table->string("IMEI", 50)->nullable();
                $table->integer("Qty")->nullable();
                $table->text("Description")->nullable();
                $table->double("Cost")->nullable();
                $table->double("Discount")->nullable();
                $table->tinyInteger("Returned")->default(0);
                $table->datetime("ReturnedDate")->nullable();
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();

                $table
                    ->foreign("InvoiceId")
                    ->references("id")
                    ->on("sales");
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
