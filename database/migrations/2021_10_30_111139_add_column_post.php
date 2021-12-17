<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_post_id')->nullable();
            $table->foreign('reply_post_id')->references('id')->on('posts');
            $table->dropColumn('reply_user_id');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('reply_user_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('reply_post_id');
            $table->json('reply_user_id')->nullable();
        });
    }
}
