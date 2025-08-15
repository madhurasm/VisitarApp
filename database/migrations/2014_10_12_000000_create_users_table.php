<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100)->nullable()->index();
            $table->string('last_name', 100)->nullable()->index();
            $table->string('name', 100)->nullable()->index();
            $table->string('username', 100)->nullable()->index();
            $table->string('email',100)->nullable();
            $table->string('country_code',10)->nullable()->index();
            $table->string('mobile',20)->nullable()->index();
            $table->string('password')->nullable();
            $table->mediumText('profile_image')->nullable();
            $table->string('location',100)->nullable();
            $table->enum('language', ['en', 'es'])->default('en');
            $table->enum('type', ['admin', 'user'])->default('user')->index();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->enum('notification', ['yes', 'no'])->default('yes')->index();
            $table->longText('reset_token')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
