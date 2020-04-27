<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PizzaBill extends Model
{
    public function pizza(){
        return $this->belongsTo(Pizza::class);
    }

    public function bill(){
        return $this->belongsTo(Bill::class);
    }
}
