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
        Schema::create('payroll_schedule', function (Blueprint $table) {
            $table->id();
            $table->enum('payrollSchedule_type',['monthly', 'weekly']);
            $table->integer('payrollSchedule_date');
            $table->integer('payrollSchedule_attendance')->nullable();
            $table->date('payrollSchedule_startDate')->nullable();
            $table->enum('payrollSchedule_cutoffDay', ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'])->nullable();
            $table->enum('payrollSchedule_monthPeriod', ['all','january','february','march','april','may','june','july','august','september','october','november','december'])->nullable();
            $table->foreignId('payrollSchedule_business')->constrained('business_branch')->onDelete('cascade');
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
        Schema::dropIfExists('payroll_schedule');
    }
};
