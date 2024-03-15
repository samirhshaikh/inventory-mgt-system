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
        Schema::table("customers", function (Blueprint $table) {
            $table->index("CustomerName");
            $table->index("ContactNo1");
            $table->index("ContactNo2");
            $table->index("City");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("customers", function (Blueprint $table) {
            $table->dropIndex("customers_customername_index");
            $table->dropIndex("customers_contactno1_index");
            $table->dropIndex("customers_contactno2_index");
            $table->dropIndex("customers_city_index");
        });
    }
};
