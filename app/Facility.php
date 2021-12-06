<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    public function restaurants() {
        return $this->belongsToMany(Restaurant::class)->withTimestamps();
    }
}
