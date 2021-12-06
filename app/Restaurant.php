<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    public function menus() {
        return $this->hasMany(Menu::class, 'restaurants_id');
    }

    public function follows() {
        return $this->hasMany(Follow::class, 'restaurants_id');
    }

    // public function restaurant_types() {
    //     return $this->belongsTo(RestaurantType::class);
    // }

    public function transactions() {
        return $this->hasOne(Transaction::class, 'restaurants_id');
    }

    public function reservations() {
        return $this->hasOne(Reservation::class, 'restaurants_id');
    }

    public function reservation_prices() {
        return $this->hasOne(ReservationPrice::class, 'restaurants_id');
    }

    public function employees() {
        return $this->hasMany(Employee::class, 'restaurants_id');
    }

    public function facilities() {
        return $this->belongsToMany(Facility::class)->withTimestamps();
    }

    public function cuisines() {
        return $this->belongsToMany(Cuisine::class)->withTimestamps();
    }

    public function users() {
        return $this->belongsTo(User::class);
    }

    public function carts() {
        return $this->hasOne(Cart::class, 'restaurants_id');
    }

    public function schedules() {
        return $this->hasOne(Schedule::class, 'restaurants_id');
    }

    public function images(){
        return $this->hasMany('App\RestoImage');
    }
}
