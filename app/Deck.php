<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    protected $fillable = [
        'name', 'description',
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function cards(){
        return $this->hasMany('App\Card');
    }
}
