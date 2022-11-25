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
        Schema::create('categories_establishments', function (Blueprint $table) {
            $table->id('category_establishment_id');
            $table->foreignId('category_id');
            $table->foreign('category_id')->references('category_id')->on('categories');
            $table->foreignId('establishment_id');
            $table->foreign('establishment_id')->references('establishment_id')->on('establishments');
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
        Schema::dropIfExists('categories_establishments');
    }
};
