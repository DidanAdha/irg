<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->integer('users_id');
            $table->foreignId('menus_id');
            $table->foreignId('restaurants_id');
            // $table->intger('promos_id');
            $table->integer('qty');
            $table->integer('total_price');
            $table->integer('discount');
            $table->integer('discounted_price');
            $table->integer('delivery');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('menus_id')->references('id')->on('menus')->onDelete('cascade');
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
        Schema::dropIfExists('carts');
    }
}
