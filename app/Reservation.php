<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
	use SoftDeletes;
    protected $dates = ['deleted_at'];
    public function restaurants() {
        return $this->belongsTo(Restaurant::class);
    }

    public function users() {
        return $this->belongsTo(User::class);
    }

    public function chatrooms() {
        return $this->hasOne(Chatroom::class, 'reservations_id');
    }
}
