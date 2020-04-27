<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function pizzas(){
        return $this->hasMany(PizzaBill::class, 'bill_id');
    }
}
