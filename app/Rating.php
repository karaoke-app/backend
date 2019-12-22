<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{

    protected $table = 'ratings';

    protected $guarded = [];

    protected $fillable = [
        'song_id', 'user_id', 'rate'
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
