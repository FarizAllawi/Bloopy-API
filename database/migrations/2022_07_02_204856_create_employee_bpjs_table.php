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
        Schema::create('employee_bpjs', function (Blueprint $table) {
            $table->id();
            $table->string('employeeBPJS_BPJSKetenagakerjaanNumber')->nullable();
            $table->string('employeeBPJS_NPPBPJSKetenagakerjaan')->nullable();
            $table->date('employeeBPJS_BPJSKetenagakerjaanDate')->nullable();
            $table->string('employeeBPJS_BPJSKesehatanNumber')->nullable();
            $table->enum('employeeBPJS_BPJSKesehatanFamily', [0,1,2,3,4,5,6,7,8,9])->nullable();
            $table->date('employeeBPJS_BPJSKesehatanDate')->nullable();
            $table->enum('employeeBPJS_BPJSKesehatanCost', ['by-company','by-employee'])->nullable();
            $table->enum('employeeBPJS_JHTCost', ['not-paid','by-employee', 'by-company', 'default'])->nullable();
            $table->enum('employeeBPJS_jaminanPensiunCost', ['not-paid','by-employee', 'by-company', 'default'])->nullable();
            $table->date('employeeBPJS_jaminanPensiunDate')->nullable();
            $table->foreignId('employeeBPJS_employee')->constrained('employee')->onDelete('cascade');;
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
        Schema::dropIfExists('employee_bpjs');
    }
};
