<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TodoListItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('todo_list_item', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('todo_list_id');
            $table->string('title');
            $table->enum('type', ['geo_point', 'address', 'rubric']);
            $table->integer('position');
            $table->integer('after');
            $table->foreign('todo_list_id')
                    ->references('id')->on('todo_list')
                    ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('todo_list_item');
    }
}
