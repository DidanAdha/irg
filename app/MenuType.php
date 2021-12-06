<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuType extends Model
{
    public function menus() {
        return $this->hasMany(Menu::class, 'menu_types_id');
    }
}
