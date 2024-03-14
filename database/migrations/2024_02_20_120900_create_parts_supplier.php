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
        if (!Schema::hasTable("parts_supplier")) {
            Schema::create("parts_supplier", function (Blueprint $table) {
                $table->id();
                $table->string("SupplierName", 255)->nullable();
                $table->string("ContactNo1", 50)->nullable();
                $table->string("ContactNo2", 50)->nullable();
                $table->string("ContactNo3", 50)->nullable();
                $table->double("Balance")->nullable();
                $table->text("Comments")->nullable();
                $table->tinyInteger("IsActive")->default(0);
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();

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
        if (Schema::hasTable("parts_supplier")) {
            Schema::dropIfExists("parts_supplier");
        }
    }
};
