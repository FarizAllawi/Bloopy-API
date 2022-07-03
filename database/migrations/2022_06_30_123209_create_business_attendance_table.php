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
        Schema::create('business_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('businessAttendance_name');
            $table->time('businessAttendance_clockIn');
            $table->time('businessAttendance_clockOut');
            $table->time('businessAttendance_breakOut')->nullable();
            $table->time('businessAttendance_breakIn')->nullable();
            $table->time('businessAttendance_overtimeBefore')->nullable();
            $table->time('businessAttendance_overtimeAfter')->nullable();
            $table->foreignId('businessAttendance_businessSchedule')->constrained('business_schedule')->onDelete('cascade');
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
        Schema::dropIfExists('business_attendance');
    }
};
