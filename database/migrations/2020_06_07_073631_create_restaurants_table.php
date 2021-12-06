<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id');
            $table->string('name');
            $table->text('desc');
            $table->double('latitude', 13, 10)->nullable();
            $table->double('longitude', 13, 10)->nullable();
            $table->string('address');
            $table->string('phone_number');
            $table->integer('cities_id');
            $table->integer('start_price');
            $table->integer('end_price');
            $table->time('open_at');
            $table->time('close_at');
            $table->string('img')->default('/storage/resto_img/default.jpg');
            $table->string('logo')->default('/storage/resto_logo/default.png');
            $table->enum('status', ['active', 'nonactive'])->default('active');
            $table->integer('ongkir')->default(0);
            $table->integer('can_reservation')->default(0);
            $table->integer('can_delivery')->default(0);
            $table->integer('can_take_away')->default(0);
            $table->integer('scheduled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
