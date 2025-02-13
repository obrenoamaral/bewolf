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
        Schema::create('client_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('question_id')->nullable();
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->unsignedBigInteger('question_multiple_choices_id')->nullable();
            $table->unsignedBigInteger('multiple_choice_answer_id')->nullable();
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('set null');
            $table->foreign('answer_id')->references('id')->on('answers')->onDelete('set null');
            $table->foreign('question_multiple_choices_id')->references('id')->on('question_multiple_choices')->onDelete('set null');
            $table->foreign('multiple_choice_answer_id')->references('id')->on('answers_multiple_choices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_answers');
    }
};
