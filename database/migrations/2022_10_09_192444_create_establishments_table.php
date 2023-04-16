<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('establishments', function (Blueprint $table) {
            $table->id('establishment_id');
            $table->string('trade_name');
            $table->string('siret');
            $table->foreignId('address_id');
            $table->foreign('address_id')->references('address_id')->on('addresses');
            $table->string('logo')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->json('opening')->nullable();
            $table->foreignId('owner_id');
            $table->foreign('owner_id')->references('owner_id')->on('owners');
            $table->foreignId('status_id');
            $table->foreign('status_id')->references('status_id')->on('status');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('establishments');
    }
};
