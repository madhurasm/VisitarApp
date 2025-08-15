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
        Schema::table('visitor_check_ins', function (Blueprint $table) {
         $table->bigInteger('host_id')->nullable()->after('purpose_of_visit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor_check_ins', function (Blueprint $table) {
            //
        });
    }
};
