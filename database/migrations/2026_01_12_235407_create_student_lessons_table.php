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
        Schema::create('student_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_unlocked')->default(false);
            $table->boolean('content_viewed')->default(false);
            $table->boolean('quiz_passed')->default(false);
            $table->integer('best_score')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('prerequisite_lesson_id')
                ->nullable()
                ->after('is_active')
                ->constrained('lessons')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_lessons');
        
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['prerequisite_lesson_id']);
            $table->dropColumn('prerequisite_lesson_id');
        });
    }
};
