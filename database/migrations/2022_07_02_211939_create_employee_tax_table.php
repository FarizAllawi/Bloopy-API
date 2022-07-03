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
        Schema::create('employee_tax', function (Blueprint $table) {
            $table->id();
            $table->string('employeeTax_npwp')->nullable();
            $table->string('employeeTax_ptkp')->nullable();
            $table->enum('employeeTax_method',['gross','gross-up','netto'])->default('gross');
            $table->enum('employeeTax_salary',['taxable','non-taxable'])->default('taxable');
            $table->date('employeeTax_taxableDate')->nullable();
            $table->integer('employeeTax_beginningNeto')->nullable();
            $table->integer('employeeTax_pph21Paid')->nullable();
            $table->foreignId('employeeTax_status')->constrained('employeeTax_status')->onDelete('cascade');
            $table->foreignId('employeeTax_employee')->constrained('employee')->onDelete('cascade');;
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
        Schema::dropIfExists('employee_tax');
    }
};
