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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('passing_score')->default(70); // percentage
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->integer('order')->default(0);
            $table->integer('points')->default(1);
            $table->timestamps();
        });

        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->char('option_letter', 1); // A, B, C, D
            $table->text('answer_text');
            $table->boolean('is_correct')->default(false);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        Schema::create('user_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('completion_time')->nullable(); // in seconds
            $table->integer('score')->nullable(); // percentage or points
            $table->boolean('passed')->default(false);
            $table->json('answers_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quiz_attempts');
        Schema::dropIfExists('answers');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
