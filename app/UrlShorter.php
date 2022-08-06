<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UrlShorter extends Model
{
    protected $fillable = [
        'name', 'url', 'short','visits'
    ];
}
