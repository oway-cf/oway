<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGeoPointsToItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('todo_list_item', function(Blueprint $table)
        {
            $table->string('lon')->nullable();
            $table->string('lat')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('todo_list_item', function(Blueprint $table){
            $table->dropColumn('lon');
            $table->dropColumn('lat');
        });
    }
}
