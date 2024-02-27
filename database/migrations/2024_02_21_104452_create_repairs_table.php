<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable("repairs")) {
            Schema::create("repairs", function (Blueprint $table) {
                $table->id();
                $table->integer("CustomerId")->nullable();
                $table->datetime("ReceivedDate")->nullable();
                $table->string("InvoiceNo", 20)->nullable();
                $table->datetime("InvoiceDate")->nullable();
                $table->tinyInteger("BusinessInvoice")->default(0);
                $table->string("PaymentMethod", 200)->nullable();
                $table->string("ChequeNo", 50)->nullable();
                $table->string("Status", 50)->nullable();
                $table->integer("MakeId")->nullable();
                $table->integer("ModelId")->nullable();
                $table->integer("ColorId")->nullable();
                $table->string("IMEI", 50)->nullable();
                $table->text("Notes")->nullable();
                $table->text("ReasonForNotRepair")->nullable();
                $table->double("Amount")->nullable();
                $table->double("VAT")->nullable();
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();

                $table
                    ->foreign("CustomerId")
                    ->references("id")
                    ->on("customers");
                $table
                    ->foreign("MakeId")
                    ->references("id")
                    ->on("manufacturermaster");
                $table
                    ->foreign("ModelId")
                    ->references("id")
                    ->on("modelmaster");
                $table
                    ->foreign("ColorId")
                    ->references("id")
                    ->on("colormaster");
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable("repairs")) {
            Schema::dropIfExists("repairs");
        }
    }
};
