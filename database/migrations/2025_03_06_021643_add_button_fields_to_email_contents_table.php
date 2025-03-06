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
        Schema::table('email_content', function (Blueprint $table) {
            $table->string('button_text')->nullable(); // Texto do botão
            $table->string('button_link')->nullable(); // Link do botão
        });
    }

    public function down()
    {
        Schema::table('email_content', function (Blueprint $table) {
            $table->dropColumn('button_text');
            $table->dropColumn('button_link');
        });
    }
};
