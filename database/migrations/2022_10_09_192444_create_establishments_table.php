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
        Schema::create('establishments', function (Blueprint $table) {
            $table->id('establishment_id');
            $table->string('trade_name');
            $table->string('siret');
            $table->string('address');
            $table->string('postal_code');
            $table->string('city');
            $table->string('logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('opening')->nullable();
            $table->boolean('checked')->default(false);
            $table->foreignId('owner_id');
            $table->foreign('owner_id')->references('owner_id')->on('owners');
            $table->foreignId('status_id');
            $table->foreign('status_id')->references('status_id')->on('status');
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
        Schema::dropIfExists('establishments');
    }
};
