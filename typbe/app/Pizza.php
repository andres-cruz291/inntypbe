<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    public function fotos(){
        return $this->hasMany(Foto::class);
    }

    public function bills(){
        return $this->hasMany(PizzaBill::class);
    }
}
