<?php

namespace App;

use App\Scopes\VerifiedSongScope;
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

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new VerifiedSongScope);
    }

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
        return $this->belongsToMany(Playlist::class, 'playlist_song');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_song');
    }
}
