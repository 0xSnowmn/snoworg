<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Info extends Model
{
    protected $fillable = [
        'mac_adress', 'version', 'program','os','Last_opened','Pc_name','count_using'
    ];
}
