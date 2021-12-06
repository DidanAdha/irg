<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address')->nullable();
            $table->date('ttl')->nullable();
            $table->integer('roles_id');
            $table->integer('employees_id')->default(0);
            $table->integer('active')->default(1);
            $table->string('phone_number');
            $table->string('img')->default('/storage/user_img/default.png');
            $table->integer('in_cart')->default(0);
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->integer('priv_admin');
            $table->string('device_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
