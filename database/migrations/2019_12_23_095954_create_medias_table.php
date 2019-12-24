<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id');

            $table->string("key")->unique();
            $table->integer("type");

            $table->string("title");
            $table->string("artists");
            $table->text("url");
            $table->text("image");

            $table->dateTime("expired_url");

            $table->timestamps();
        });

        \DB::statement('ALTER TABLE medias ADD FULLTEXT full_media_title(title)');
        \DB::statement('ALTER TABLE medias ADD FULLTEXT full_media_artists(artists)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medias');
    }
}
