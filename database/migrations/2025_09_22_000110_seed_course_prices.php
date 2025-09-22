<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing courses by title
        DB::table('courses')->where('title', 'Prosperity Fundamentals')->update(['price' => 8000]);
        DB::table('courses')->where('title', 'Prosperity Investments')->update(['price' => 13000]);
        DB::table('courses')->where('title', 'Prosperity Legacy')->update(['price' => 15000]);
    }

    public function down(): void
    {
        DB::table('courses')->whereIn('title', [
            'Prosperity Fundamentals',
            'Prosperity Investments',
            'Prosperity Legacy',
        ])->update(['price' => null]);
    }
};

