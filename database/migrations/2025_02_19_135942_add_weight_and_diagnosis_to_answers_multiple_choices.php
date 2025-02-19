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
            $table->integer('weight')->after('answer'); // Adiciona a coluna weight após answer
            $table->string('diagnosis')->after('weight'); // Adiciona a coluna diagnosis após weight
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_multiple_choices', function (Blueprint $table) {
            $table->dropColumn(['weight', 'diagnosis']);
        });
    }
};

