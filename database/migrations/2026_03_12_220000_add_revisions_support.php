<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add section_code to sections
        Schema::table('sections', function (Blueprint $table) {
            $table->string('section_code', 10)->unique()->nullable()->after('description');
        });

        // 2. Add created_by to lessons (tracks teacher-created lessons)
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('id');
        });

        // 3. Section-Lessons pivot (which lessons belong to which section)
        Schema::create('section_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['section_id', 'lesson_id']);
        });

        // 4. Add image_path to scenario_items (for image-based simulations)
        Schema::table('scenario_items', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('description');
        });

        // 5. Assessment attempts (pre/post assessment)
        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['pre', 'post']);
            $table->integer('score')->default(0);
            $table->integer('total_questions')->default(0);
            $table->json('answers_data')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('completion_time')->nullable(); // seconds
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('section_lessons');
        
        Schema::table('scenario_items', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
        
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });
        
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('section_code');
        });
    }
};
