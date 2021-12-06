<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public function restaurants() {
        return $this->belongsTo(Restaurant::class);
    }

    public function menu_types() {
        return $this->belongsTo(MenuType::class);
    }

    public function bookmarks() {
        return $this->hasMany(Bookmark::class, 'menus_id');
    }

    public function carts() {
        return $this->hasOne(Cart::class, 'menus_id');
    }

    public function transaction_details() {
        return $this->hasOne(TransactionDetail::class, 'menus_id');
    }

    public function promos() {
        return $this->hasOne(Promo::class, 'menus_id');
    }

}
