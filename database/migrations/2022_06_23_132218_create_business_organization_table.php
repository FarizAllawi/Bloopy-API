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
        Schema::create('business_organization', function (Blueprint $table) {
            $table->id();
            $table->string('businessOrganization_name');
            $table->bigInteger('businessOrganization_parent')->nullable()->default(0);   
            $table->unsignedBigInteger('businessOrganization_business');       
            $table->foreign('businessOrganization_business')->references('id')->on('business_branch')->onDelete('cascade');
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
        Schema::dropIfExists('business_organization');
    }
};
