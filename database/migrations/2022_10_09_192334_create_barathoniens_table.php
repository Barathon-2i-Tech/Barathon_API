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
        Schema::create('barathoniens', function (Blueprint $table) {
            $table->id('barathonien_id');
            $table->date('birthday');
            $table->string('address');
            $table->string('postal_code');
            $table->string('city');
            $table->string('avatar')->nullable();;
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
        Schema::dropIfExists('barathoniens');
    }
};
