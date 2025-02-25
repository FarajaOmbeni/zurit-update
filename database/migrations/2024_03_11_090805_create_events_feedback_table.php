<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events_feedback', function (Blueprint $table) {
            $table->id();
            $table->String('name');
            $table->String('venue');
            $table->String('comprehensiveness');
            $table->String('relevance');
            $table->String('recommendation');
            $table->String('return_client');
            $table->String('value_for_money');
            $table->String('valuable_aspect');
            $table->String('improvement');
            $table->String('suggestion');
            $table->String('fav_trainor');
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_feedback');
    }
};
