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
}
