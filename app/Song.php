<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    /**
     * @var string
     */
    protected $table = 'songs';
    protected $hidden = ['pivot'];

    /**
     * @var array
     */
    protected $guarded = [];

    protected $fillable = [
        'title', 'cues', 'is_accepted', 'artist',
    ];

    protected $casts = [
        'cues' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_songs');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'song_categories');
    }
}
