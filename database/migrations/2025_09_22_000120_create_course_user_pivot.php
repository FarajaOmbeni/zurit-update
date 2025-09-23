<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_user')) {
            // Table already exists (possibly from a partial run). Skip creation.
            return;
        }
        Schema::create('course_user', function (Blueprint $table) {
            // Be explicit to avoid FK mismatches on some MySQL setups
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->unique(['course_id', 'user_id']);
            // Foreign key constraints omitted to avoid platform-specific errors
            // Consider a follow-up migration to add FKs once verified.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_user');
    }
};
