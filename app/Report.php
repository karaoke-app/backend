<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /**
     * @var string
     */
    protected $table = 'reports';

    /**
     * @var array
     */
    protected $guarded = [];

    protected $fillable = [
        'description',
    ];
}
