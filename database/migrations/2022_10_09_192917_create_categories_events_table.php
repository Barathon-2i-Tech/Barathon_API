<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('categories_events', function (Blueprint $table) {
            $table->id('category_event_id');
            $table->foreignId('category_id');
            $table->foreign('category_id')->references('category_id')->on('categories');
            $table->foreignId('event_id');
            $table->foreign('event_id')->references('event_id')->on('events');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories_events');
    }
};
