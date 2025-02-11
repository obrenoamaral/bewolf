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
        Schema::table('client_answers', function (Blueprint $table) {
            // Relacionamento com perguntas de múltipla escolha
            $table->foreignId('question_multiple_choices_id')
                ->nullable()
                ->constrained('question_multiple_choices')
                ->onDelete('cascade');

            // Relacionamento com respostas de múltipla escolha
            $table->foreignId('multiple_choice_answer_id')
                ->nullable()
                ->constrained('answers_multiple_choices')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_answers', function (Blueprint $table) {
            // Remover as chaves estrangeiras antes de excluir as colunas
            $table->dropForeign(['question_multiple_choices_id']);
            $table->dropForeign(['multiple_choice_answer_id']);

            // Remover as colunas
            $table->dropColumn('question_multiple_choices_id');
            $table->dropColumn('multiple_choice_answer_id');
        });
    }
};
