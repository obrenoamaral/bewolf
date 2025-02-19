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
            $table->text('diagnosis')->change(); // Altera a coluna para TEXT
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_multiple_choices', function (Blueprint $table) {
            $table->string('diagnosis', 255)->change(); // Volta para VARCHAR(255)
        });
    }
};
