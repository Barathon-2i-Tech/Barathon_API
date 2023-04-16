<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreignId('event_id');
            $table->foreign('event_id')->references('event_id')->on('events');
            $table->boolean('isFav')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
