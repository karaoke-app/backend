<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlaylistSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playlist_songs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('playlist_id');
            $table->unsignedInteger('song_id');
            $table->timestamps();
            $table->foreign('playlist_id')->references('id')->on('playlists')
                ->onDelete('cascade');
            $table->foreign('song_id')->references('id')->on('songs')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playlist_songs');
    }
}
