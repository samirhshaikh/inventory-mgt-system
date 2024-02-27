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
        if (!Schema::hasTable("sales")) {
            Schema::create("sales", function (Blueprint $table) {
                $table->id();
                $table->integer("CustomerId")->nullable();
                $table->integer("RepairId")->nullable();
                $table->string("InvoiceNo", 20)->nullable();
                $table->datetime("InvoiceDate")->nullable();
                $table->tinyInteger("BusinessInvoice")->default(0);
                $table->string("PaymentMethod", 200)->nullable();
                $table->string("ChequeNo", 50)->nullable();
                $table->text("Comments")->nullable();
                $table->double("VAT")->nullable();
                $table->tinyInteger("IsActive")->default(0);
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();
                $table->text("AccessoriesDesc")->nullable();
                $table->double("AccessoriesAmount")->nullable();

                $table
                    ->foreign("CustomerId")
                    ->references("id")
                    ->on("customers");
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
        if (Schema::hasTable("sales")) {
            Schema::dropIfExists("sales");
        }
    }
};
