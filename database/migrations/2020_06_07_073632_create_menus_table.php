<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurants_id');
            $table->string('name');
            $table->text('desc');
            $table->integer('price');
            $table->integer('delivery_price')->default(0);
            $table->integer('is_delivery')->default(0);
            $table->integer('menu_types_id');
            $table->integer('is_favorite')->default(0);
            $table->string('img')->default('/storage/menu_img/default.jpg');
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
        Schema::dropIfExists('menus');
    }
}
