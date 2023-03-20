<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('owners', function (Blueprint $table) {
            $table->id('owner_id');
            $table->string('siren');
            $table->longText('kbis');
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable();
            $table->foreignId('status_id');
            $table->foreign('status_id')->references('status_id')->on('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('owners');
    }
};
