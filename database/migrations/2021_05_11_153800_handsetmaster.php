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
        if (!Schema::hasTable("handsetmaster")) {
            Schema::create("handsetmaster", function (Blueprint $table) {
                $table->id();
                $table->string("Name", 255)->nullable();
                $table->integer("MakeId")->nullable();
                $table->integer("ModelId")->nullable();
                $table->integer("ColorId")->nullable();
                $table->tinyInteger("IsActive")->default(0);
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();

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
        if (Schema::hasTable("handsetmaster")) {
            Schema::dropIfExists("handsetmaster");
        }
    }
};
