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
        Schema::create('answers_multiple_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_multiple_choice_id')->constrained('question_multiple_choices')->onDelete('cascade'); // Nome da tabela corrigido
            $table->string('answer');
            $table->integer('weight');
            $table->text('diagnosis');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers_multiple_choices');
    }
};
