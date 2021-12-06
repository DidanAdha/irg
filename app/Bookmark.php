<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    public function menus() {
        return $this->belongsTo(Menu::class);
    }

    public function users() {
        return $this->belongsTo(User::class);
    }
}
