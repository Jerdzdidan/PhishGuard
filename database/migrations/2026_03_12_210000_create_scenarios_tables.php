<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Scenarios table (replaces hardcoded getSimulationConfig)
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique(); // e.g. 'lesson-1-sim'
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['pre_assessment', 'post_assessment', 'simulation'])->default('simulation');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Scenario items table (individual questions/challenges within a scenario)
        Schema::create('scenario_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable(); // JSON or HTML content for simulation display
            $table->string('correct_action')->nullable(); // The correct answer/action
            $table->json('options')->nullable(); // Available options/actions
            $table->json('hints')->nullable(); // Hints for the user
            $table->json('metadata')->nullable(); // Additional item-specific data
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scenario_items');
        Schema::dropIfExists('scenarios');
    }
};
