<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSharestatusmeAndSharestatusfrToFriendRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('friend_relations', function (Blueprint $table) {
            $table->boolean('sharestatusme');
            $table->boolean('sharestatusfr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('friend_relations', function (Blueprint $table) {
            $table->dropColumn('sharestatusme');
            $table->dropColumn('sharestatusfr');
        });
    }
}
