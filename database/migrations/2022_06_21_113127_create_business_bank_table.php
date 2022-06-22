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
        Schema::create('business_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('businessBank_business')->constrained('business')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('businessBank_bankAccount')->constrained('bank_account')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('business_bank');
    }
};
