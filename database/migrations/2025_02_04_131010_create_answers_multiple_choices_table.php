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
            $table->unsignedBigInteger('question_multiple_choice_id');
            $table->string('answer');
            $table->timestamps();

            $table->foreign('question_multiple_choice_id')->references('id')->on('question_multiple_choices')->onDelete('cascade');
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
