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
        Schema::create('business_branch', function (Blueprint $table) {
            $table->id();
            $table->string('businessBranch_name');
            $table->foreignId('businessBranch_business')->constrained('business')->onDelete('cascade');
            $table->integer('businessBranch_employee');
            $table->string('businessBranch_email')->unique();
            $table->string('businessBranch_phone')->unique();
            $table->string('businessBranch_BPJSKetenagakerjaan')->nullable();
            $table->string('businessBranch_BPJSJKK')->nullable();
            $table->string('businessBranch_NPWPCode')->nullable();
            $table->string('businessBranch_KLUCode')->nullable();
            $table->string('businessBranch_signaturWithCompanyStamp')->nullable();
            $table->enum('businessBranch_status', ['central','branch']);
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
        Schema::dropIfExists('business_branch');
    }
};
