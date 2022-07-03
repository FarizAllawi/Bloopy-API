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
        Schema::create('business_address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('businessAddress_address')->constrained('address')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('businessAddress_business')->constrained('business_branch')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('businessAddress_type',['shipping','billing','business','identity']);
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
        Schema::dropIfExists('business_address');
    }
};
