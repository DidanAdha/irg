<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
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
        return $this->hasOne(Chatroom::class, 'transactions_id');
    }

    public function transaction_details() {
        return $this->hasMany(TransactionDetail::class, 'transactions_id');
    }
}
