<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('administrators', function (Blueprint $table) {
            $table->id('administrator_id');
            $table->boolean('superAdmin')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('administrators');
    }
};
