<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    /**
     * @var string
     */
    protected $table = 'songs';

    /**
     * @var array
     */
    protected $guarded = [];

    protected $fillable = [
        'title', 'songText', 'category', 'artist',
    ];

    protected $casts = [
        'songText' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
