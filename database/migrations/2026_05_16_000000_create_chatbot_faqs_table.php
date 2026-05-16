<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category', 160);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('question');
            $table->mediumText('answer');
            /** @description Lowercase substring triggers (pipe "|" separated); optional boosts for matching paraphrases. */
            $table->string('match_signals', 2000)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_faqs');
    }
};
