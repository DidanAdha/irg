<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('users_id');
            $table->integer('restaurants_id');
            $table->integer('restaurant_tables_id')->nullable();
            $table->integer('total_qty');
            $table->integer('total_price');
            $table->integer('total_discount');
            $table->integer('discounted_price');
            $table->integer('ongkir')->default(0);
            $table->integer('total_end');
            $table->enum('status', ['pending', 'process', 'ready', 'finished', 'decline'])->default('pending');
            $table->integer('is_done')->default(0);
            $table->integer('delivery');
            $table->integer('take_away');
            $table->string('address')->nullable();
            $table->integer('resto_edit')->default(0);
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
        Schema::dropIfExists('transactions');
    }
}
