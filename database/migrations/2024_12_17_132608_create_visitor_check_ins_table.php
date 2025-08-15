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
        Schema::create('visitor_check_ins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable()->index();
            $table->string('email',100)->nullable()->index();
            $table->string('country_code',10)->nullable()->index();
            $table->string('mobile',20)->nullable()->index();
            $table->string('company_name')->nullable();
            $table->longText('purpose_of_visit')->nullable();
            $table->string('host_name')->nullable();
            $table->mediumText('profile_image')->nullable();
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_check_ins');
    }
};
