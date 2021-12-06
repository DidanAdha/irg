<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    public function menus() {
        return $this->belongsTo(Menu::class);
    } 
}
