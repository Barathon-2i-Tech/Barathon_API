<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('barathoniens', function (Blueprint $table) {
            $table->id('barathonien_id');
            $table->date('birthday');
            $table->foreignId('address_id');
            $table->foreign('address_id')->references('address_id')->on('addresses');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barathoniens');
    }
};
