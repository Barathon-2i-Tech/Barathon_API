<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('establishments_employees', function (Blueprint $table) {
            $table->id('establishment_employee_id');
            $table->foreignId('establishment_id');
            $table->foreign('establishment_id')->references('establishment_id')->on('establishments');
            $table->foreignId('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('establishments_employees');
    }
};
