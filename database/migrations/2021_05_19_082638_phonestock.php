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
        if (!Schema::hasTable("phonestock")) {
            Schema::create("phonestock", function (Blueprint $table) {
                $table->id();
                $table->integer("InvoiceId")->nullable();
                $table->integer("MakeId")->nullable();
                $table->integer("ModelId")->nullable();
                $table->integer("ColorId")->nullable();
                $table->string("Size", 50)->nullable();
                $table->string("IMEI", 50)->nullable();
                $table->double("Cost")->nullable();
                $table->string("StockType", 15)->nullable();
                $table->string("ModelNo", 30)->nullable();
                $table->string("Network", 50)->nullable();
                $table->string("Status", 20)->nullable();
                $table->tinyInteger("IsActive")->default(0);
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();
                $table->integer("ParentId")->nullable();

                $table
                    ->foreign("InvoiceId")
                    ->references("id")
                    ->on("purchase");
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
        if (Schema::hasTable("phonestock")) {
            Schema::dropIfExists("phonestock");
        }
    }
};
