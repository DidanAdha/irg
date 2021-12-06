<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public function users() {
        return $this->hasOne(User::class, 'employees_id');
    }

    public function restaurants() {
        return $this->belongsTo(Restaurant::class);
    }
}
