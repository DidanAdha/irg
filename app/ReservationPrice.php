<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReservationPrice extends Model
{
    public function restaurants() {
        return $this->belongsTo(Restaurant::class);
    }
}
