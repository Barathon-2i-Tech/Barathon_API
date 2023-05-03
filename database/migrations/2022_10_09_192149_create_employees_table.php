<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employee_id');
            $table->date('hiring_date');
            $table->date('dismissal_date')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
