<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function follows() {
        return $this->hasMany(Follow::class, 'users_id');
    }

    public function bookmarks() {
        return $this->hasMany(Bookmark::class, 'users_id');
    }

    public function transactions() {
        return $this->hasOne(Transaction::class, 'users_id');
    }

    public function reservations() {
        return $this->hasOne(Reservation::class, 'users_id');
    }

    public function carts() {
        return $this->hasOne(Cart::class, 'users_id');
    }

    public function employees() {
        return $this->belongsTo(Employee::class);
    }

    public function restaurants() {
        return $this->hasMany(Restaurant::class, 'users_id');
    }
}
