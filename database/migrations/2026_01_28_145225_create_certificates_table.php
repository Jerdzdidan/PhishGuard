<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->timestamp('issued_at');
            $table->integer('total_lessons_completed');
            $table->decimal('average_quiz_score', 5, 2)->nullable();
            $table->decimal('average_simulation_score', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
