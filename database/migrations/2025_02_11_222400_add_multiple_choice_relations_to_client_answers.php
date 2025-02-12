<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_answers', function (Blueprint $table) {
            $table->foreignId('question_multiple_choices_id')
                ->nullable()
                ->constrained('question_multiple_choices')
                ->onDelete('set null'); // Mudança aqui

            $table->foreignId('multiple_choice_answer_id')
                ->nullable()
                ->constrained('answers_multiple_choices')
                ->onDelete('set null'); // Mudança aqui
        });
    }

    public function down(): void
    {
        Schema::table('client_answers', function (Blueprint $table) {
            $table->dropForeign(['question_multiple_choices_id']);
            $table->dropForeign(['multiple_choice_answer_id']);

            $table->dropColumn('question_multiple_choices_id');
            $table->dropColumn('multiple_choice_answer_id');
        });
    }
};
