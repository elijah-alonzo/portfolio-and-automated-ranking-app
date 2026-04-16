<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->enum('branch', ['Executive', 'Legislative', 'Judiciary', 'Mayoral', 'Coordinator']);
            $table->unsignedInteger('hierarchy');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('council_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('council_id')->constrained('councils')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->unsignedInteger('max_slots');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['council_id', 'position_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_positions');
        Schema::dropIfExists('positions');
    }
};
