<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'surname', 'email', 'user_id_create','user_id_update',
    ];
}
