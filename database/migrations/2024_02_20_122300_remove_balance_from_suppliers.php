<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `supplier` CHANGE `CurrentBalance` `Balance` DOUBLE NULL"
        );

        Schema::table("supplier", function (Blueprint $table) {
            $table->dropColumn("InitialBalance");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            "ALTER TABLE `supplier` CHANGE `Balance` `CurrentBalance` DOUBLE NULL"
        );

        Schema::table("supplier", function (Blueprint $table) {
            $table
                ->double("InitialBalance")
                ->nullable()
                ->after("CurrentBalance");
        });
    }
};
