<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//Modelo del customer
class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    
    protected $fillable = [
        'name', 'surname', 'email', 'photo', 'user_id_created','user_id_updated',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'thumbnail',
    ];
}
