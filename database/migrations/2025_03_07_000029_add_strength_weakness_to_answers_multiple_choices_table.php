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
        Schema::table('answers_multiple_choices', function (Blueprint $table) {
            $table->string('strength_weakness_title')->nullable()->after('diagnosis');
            $table->enum('strength_weakness', ['strong', 'weak'])->nullable()->after('strength_weakness_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_multiple_choices', function (Blueprint $table) {
            $table->dropColumn(['strength_weakness_title', 'strength_weakness']);
        });
    }
};
