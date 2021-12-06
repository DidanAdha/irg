<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function menu() {
        return $this->belongsTo('App\Menu', 'menus_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    // public function restaurants() {
    //     return $this->belongsTo(Restaurant::class);
    // }
}
