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
        Schema::create('events', function (Blueprint $table) {
            $table->id('event_id');
            $table->string('event_name');
            $table->text('description');
            $table->dateTime('start_event');
            $table->dateTime('end_event');
            $table->string('poster')->nullable();
            $table->decimal('price')->nullable();
            $table->integer('capacity')->nullable();
            $table->foreignId('establishment_id');
            $table->foreign('establishment_id')->references('establishment_id')->on('establishments');
            $table->foreignId('status_id');
            $table->foreign('status_id')->references('status_id')->on('status');
            $table->foreignId('user_id');
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('event_update_id')->nullable();
            $table->foreign('event_update_id')->references('event_id')->on('events');
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
        Schema::dropIfExists('events');
    }
};
