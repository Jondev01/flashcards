<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'front', 'back',
    ];

    public function deck(){
        return $this->belongsTo('App\Deck');
    }
}
