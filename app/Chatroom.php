<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
    public function users() {
        return $this->belongsTo(User::class);
    }

    public function restaurants() {
        return $this->belongsTo(Restaurant::class);
    }

    public function transactions() {
        return $this->belongsTo(Transaction::class);
    }

    public function reservations() {
        return $this->belongsTo(Reservation::class);
    }
}
