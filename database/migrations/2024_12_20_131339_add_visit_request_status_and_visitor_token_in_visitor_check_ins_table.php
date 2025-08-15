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
            $table->enum('visit_request_status', ['pending', 'accepted', 'rejected'])->default('pending')->after('check_out');
            $table->longText('visitor_token')->nullable()->after('visit_request_status');
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
