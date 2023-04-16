<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->string('address');
            $table->string('postal_code');
            $table->string('city');
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
};
