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
            $table->string('diagnosis')->after('answer'); // Adiciona a coluna diagnosis após answer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_multiple_choices', function (Blueprint $table) {
            $table->dropColumn('diagnosis');
        });
    }
};

