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
        Schema::create('evaluation_peer_evaluators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->foreignId('evaluatee_user_id')->constrained('users')->onDelete('cascade')->comment('The student being evaluated');
            $table->foreignId('evaluator_user_id')->constrained('users')->onDelete('cascade')->comment('The peer evaluator');
            $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('cascade')->comment('The adviser who assigned this');
            $table->text('assignment_notes')->nullable();
            $table->timestamp('assigned_at');
            $table->timestamps();
            $table->unique(
                ['evaluation_id', 'evaluatee_user_id', 'evaluator_user_id'],
                'unique_peer_evaluator_assignment'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_peer_evaluators');
    }
};
