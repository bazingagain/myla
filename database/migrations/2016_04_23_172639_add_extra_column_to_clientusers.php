<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraColumnToClientusers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_users', function (Blueprint $table) {
            $table->string('pic_url');
            $table->string('nick_name');
            $table->string('sex');
            $table->string('address');
            $table->string('signature');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_users', function (Blueprint $table) {
            $table->dropColumn('pic_url');
            $table->dropColumn('nick_name');
            $table->dropColumn('sex');
            $table->dropColumn('address');
            $table->dropColumn('signature');
        });
    }
}
