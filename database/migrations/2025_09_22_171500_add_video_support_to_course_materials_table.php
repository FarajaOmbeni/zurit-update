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
        Schema::table('course_materials', function (Blueprint $table) {
            $table->enum('type', ['pdf', 'video'])->default('pdf')->after('file_size');
            $table->string('uploadthing_url')->nullable()->after('type');
            $table->string('uploadthing_key')->nullable()->after('uploadthing_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_materials', function (Blueprint $table) {
            $table->dropColumn(['type', 'uploadthing_url', 'uploadthing_key']);
        });
    }
};
