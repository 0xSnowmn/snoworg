<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activates extends Model
{
    protected $fillable = [
        'active_request', 'program', 'user','pass','expire','mac','used','activate_at','version'
    ];
    public function program()
    {
        return $this->hasOne('App\programs','id','program');
    }
}
