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
        Schema::create('business_jobPosition', function (Blueprint $table) {
            $table->id();
            $table->string('businessJobPosition_name');
            $table->bigInteger('businessJobPosition_parent')->nullable()->default(0);
            $table->text('businessJobPosition_description')->nullable();
            $table->unsignedBigInteger('businessJobPosition_organization');       
            $table->unsignedBigInteger('businessJobPosition_jobLevel');       
            $table->foreign('businessJobPosition_jobLevel')->references('id')->on('business_jobLevel')->onDelete('cascade');
            $table->foreign('businessJobPosition_organization')->references('id')->on('business_organization')->onDelete('cascade');
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
        Schema::dropIfExists('business_jobPosition');
    }
};
