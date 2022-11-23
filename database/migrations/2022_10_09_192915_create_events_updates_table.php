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
        Schema::create('events_updates', function (Blueprint $table) {
            $table->id('event_update_id');
            $table->foreignId('event_id');
            $table->foreign('event_id')->references('event_id')->on('events');
            $table->string('event_name');
            $table->text('description');
            $table->dateTime('start_event');
            $table->dateTime('end_event');
            $table->string('poster')->nullable();
            $table->decimal('price')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('rejected')->default(false);
            $table->string('establishment_id');
            $table->string('status_id');
            $table->string('user_id');
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
        Schema::dropIfExists('events_updates');
    }
};
