<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurants_id');
            $table->integer('chair4')->default(0);
            $table->integer('chair8')->default(0);
            $table->integer('chair12')->default(0);
            $table->integer('chair16')->default(0);
            $table->integer('etc')->default(0);
            $table->timestamps();

            $table->foreign('restaurants_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_prices');
    }
}
