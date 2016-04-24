<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clientName');
            $table->string('email');
            $table->string('content');
            $table->timestamps();
            $table->foreign('clientName')
                ->references('clientName')->on('client_users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign('feedbacks_client_users_clientName_foreign');
        Schema::drop('feedbacks');

    }
}
