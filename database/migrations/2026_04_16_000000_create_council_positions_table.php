<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('council_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('council_id')->constrained('councils')->onDelete('cascade');
            $table->string('title');
            $table->unsignedInteger('max_slots');
            $table->string('branch')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['council_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_positions');
    }
};
