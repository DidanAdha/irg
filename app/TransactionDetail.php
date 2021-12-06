<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    public function menus() {
        return $this->belongsTo(Menu::class);
    }

    public function transactions() {
        return $this->belongsTo(Transaction::class);
    }
}
