<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public function restaurants() {
        return $this->belongsTo(Restaurant::class);
    }

    public function users() {
        return $this->belongsTo(User::class);
    }

}
