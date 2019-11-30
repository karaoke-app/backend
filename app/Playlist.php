<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    /**
     * @var string
     */
    protected $table = 'playlists';
    protected $hidden = ['pivot'];

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
