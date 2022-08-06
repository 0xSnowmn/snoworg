<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class programs extends Model
{
    protected $fillable = [
        'program', 'price', 'price','version','url','downloads'
    ];
}
