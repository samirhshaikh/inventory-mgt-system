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
        if (!Schema::hasTable("app_settings")) {
            Schema::create("app_settings", function (Blueprint $table) {
                $table->string("UserName", 20);
                $table->string("State", 20);
                $table->text("Payload");
                $table
                    ->datetime("CreatedDate")
                    ->nullable()
                    ->default("NULL");
                $table
                    ->string("CreatedBy", 20)
                    ->nullable()
                    ->default("NULL");
                $table
                    ->datetime("UpdatedDate")
                    ->nullable()
                    ->default("NULL");
                $table->primary(["UserName", "State"]);

                $table
                    ->foreign("UserName")
                    ->references("UserName")
                    ->on("user");
                $table
                    ->foreign("CreatedBy")
                    ->references("UserName")
                    ->on("user");
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable("app_settings")) {
            Schema::dropIfExists("app_settings");
        }
    }
};
