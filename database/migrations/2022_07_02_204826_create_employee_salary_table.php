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
        Schema::create('employee_salary', function (Blueprint $table) {
            $table->id();
            $table->integer('employeeSalary_basicSalary');
            $table->enum('employeeSalary_type',['monthly','weekly']);
            $table->enum('employeeSalary_prorateSetting', ['based-on-working-day','based-on-calendar-day','custom-on-working-day','custom-on-calendar-day']);
            $table->enum('employeeSalary_overtime',['yes','no']);
            $table->foreignId('employeeSalary_employee')->constrained('employee')->onDelete('cascade');;
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
        Schema::dropIfExists('employee_salary');
    }
};
