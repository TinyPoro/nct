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
            $table->enum("type", [\App\Models\Media::VIDEO_TYPE, \App\Models\Media::AUDIO_TYPE]);

            $table->string("title");
            $table->string("artists");
            $table->text("url");
            $table->text("image");

            $table->unsignedInteger("playlist_id");

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
        Schema::dropIfExists('medias');
    }
}
