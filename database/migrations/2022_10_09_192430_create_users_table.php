<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->foreignId('owner_id')->nullable();
            $table->foreign('owner_id')->references('owner_id')->on('owners');
            $table->foreignId('barathonien_id')->nullable();
            $table->foreign('barathonien_id')->references('barathonien_id')->on('barathoniens');
            $table->foreignId('administrator_id')->nullable();
            $table->foreign('administrator_id')->references('administrator_id')->on('administrators');
            $table->foreignId('employee_id')->nullable();
            $table->foreign('employee_id')->references('employee_id')->on('employees');
            $table->rememberToken();
            $table->timestamp('deleted_at')->nullable();
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
};
