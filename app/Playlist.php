<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    /**
     * @var string
     */
    protected $table = 'playlists';

    /**
     * @var array
     */
    protected $guarded = [];

    protected $fillable = [
        'name',
    ];
    public function songs()
    {
        return $this->belongsToMany(Song::class, 'playlist_songs');
    }
}
