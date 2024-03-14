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
        if (!Schema::hasTable("parts")) {
            Schema::create("parts", function (Blueprint $table) {
                $table->id();
                $table->string("Name", 255)->nullable();
                $table->tinyInteger("IsActive")->default(0);
                $table->datetime("CreatedDate")->nullable();
                $table->string("CreatedBy", 250)->nullable();
                $table->datetime("UpdatedDate")->nullable();
                $table->string("UpdatedBy", 250)->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable("parts")) {
            Schema::dropIfExists("parts");
        }
    }
};
