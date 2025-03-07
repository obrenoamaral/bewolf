<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('client_answers', function (Blueprint $table) {
            $table->string('question_type')->nullable(); // Adiciona a coluna
        });
    }

    public function down()
    {
        Schema::table('client_answers', function (Blueprint $table) {
            $table->dropColumn('question_type'); // Remove a coluna (no rollback)
        });
    }
};
