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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->nullable();
            $table->string('user_username')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('user_phone')->nullable();
            $table->enum('user_gender', ['male','female','other'])->nullable();
            $table->enum('user_role',['developer', 'admin', 'user', 'guest']);
            $table->string('user_birthPlace')->nullable();
            $table->date('user_birthDate')->nullable();
            $table->enum('user_identityType' ,['id-card', 'passport'])->nullable();
            $table->string('user_identityNumber')->nullable();
            $table->date('user_identityExpiryDate')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
