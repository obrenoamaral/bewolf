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
        Schema::table('question_multiple_choices', function (Blueprint $table) {
            $table->string('solution_title')->nullable()->after('question_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_multiple_choices', function (Blueprint $table) {
            $table->dropColumn('solution_title');
        });
    }
};
