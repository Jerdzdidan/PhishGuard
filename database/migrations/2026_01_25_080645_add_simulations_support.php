<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->boolean('has_simulation')->default(false)->after('is_active');
        });

        Schema::create('simulation_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('simulation_id'); 
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('score')->default(0);
            $table->integer('total_scenarios'); 
            $table->integer('time_taken')->nullable(); 
            $table->json('click_data')->nullable(); 
            $table->json('scenario_results')->nullable(); 
            $table->integer('attempt_number')->default(1); 
            $table->timestamps();
            
            $table->index(['user_id', 'lesson_id', 'simulation_id']);
        });

        Schema::table('student_lessons', function (Blueprint $table) {
            $table->boolean('simulations_completed')->default(false)->after('quiz_passed');
            $table->integer('simulation_progress')->default(0)->after('simulations_completed'); // Track how many sims completed
        });
    }

    public function down(): void
    {
        Schema::table('student_lessons', function (Blueprint $table) {
            $table->dropColumn(['simulations_completed', 'simulation_progress']);
        });
        
        Schema::dropIfExists('simulation_attempts');
        
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('has_simulation');
        });
    }
};
