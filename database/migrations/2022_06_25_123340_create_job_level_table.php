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
        Schema::create('business_jobLevel', function (Blueprint $table) {
            $table->id();
            $table->string('businessJobLevel_name');
            $table->unsignedBigInteger('businessJobLevel_business');       
            $table->foreign('businessJobLevel_business')->references('id')->on('business_branch')->onDelete('cascade');
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
        Schema::dropIfExists('business_jobLevel');
    }
};
