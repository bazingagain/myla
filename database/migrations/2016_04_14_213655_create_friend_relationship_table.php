<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendRelationshipTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userID');
            $table->integer('friendID');
            $table->foreign('userID')->references('id')->on('client_users');
            $table->foreign('friendID')->references('id')->on('client_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::drop('friend_relationships');
    }
}
