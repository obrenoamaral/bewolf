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
        Schema::create('email_content', function (Blueprint $table) {
            $table->id();
            $table->text('greeting')->nullable(); // Saudação
            $table->text('intro_text')->nullable(); // Texto de introdução
            $table->text('closing_text')->nullable(); // Texto de fechamento
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_content');
    }

};
