<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlists', function (Blueprint $table) {
            $table->increments('id');

            $table->text("url");
            $table->string("md5_url")->unique();

            $table->string("name")->nullable();
            $table->string("artist")->nullable();
            $table->text("image")->nullable();

            $table->integer("status")->default(\App\Models\Playlist::NOT_CRAWL_STATUS);
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
        Schema::dropIfExists('playlists');
    }
}
