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
        if (!Schema::hasTable("repairs_parts")) {
            Schema::create("repairs_parts", function (Blueprint $table) {
                $table->id();
                $table->integer("RepairId");
                $table->integer("SupplierId");
                $table->integer("PartID");
                $table->double("Cost")->nullable();
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();

                $table
                    ->foreign("SupplierID")
                    ->references("id")
                    ->on("supplier");
                $table
                    ->foreign("PartID")
                    ->references("id")
                    ->on("parts");
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
        if (Schema::hasTable("repairs_parts")) {
            Schema::dropIfExists("repairs_parts");
        }
    }
};
