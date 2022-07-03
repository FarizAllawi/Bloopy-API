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
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('employee_barcode');
            $table->foreignId('employee_userBusiness')->constrained('user_business')->onDelete('cascade');
            $table->enum('employee_status', ['permanent','contract', 'probation']);
            $table->date('employee_joinDate');
            $table->date('employee_endDate')->nullable();
            $table->enum('employee_maritalStatus', ['singgle','married','widow','widower'])->nullable();
            $table->enum('employee_religion', ['islam','catholic','christian','buddha','hindu','confucius','other'])->nullable();
            $table->enum('employee_bloodType', ['A','B','AB','O'])->nullable();
            $table->foreignId('employee_business')->constrained('business_branch')->onDelete('cascade');
            $table->foreignId('employee_organization')->constrained('business_organization')->onDelete('cascade');
            $table->foreignId('employee_jobLevel')->constrained('business_jobLevel')->onUpdate('cascade');
            $table->foreignId('employee_jobPosition')->constrained('business_jobPosition')->onUpdate('cascade');
            $table->enum('employee_grade', ['A','B','C'])->nullable();
            $table->enum('employee_class', ['1','2','3'])->nullable();
            $table->foreignId('employee_schedule')->constrained('business_schedule')->onDelete('cascade');;
            $table->foreignId('employee_paymentSchedule')->constrained('payroll_schedule')->onDelete('cascade');;
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
        Schema::dropIfExists('employee');
    }
};
