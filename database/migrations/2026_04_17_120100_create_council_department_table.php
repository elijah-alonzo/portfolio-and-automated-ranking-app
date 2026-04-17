<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('council_department', function (Blueprint $table) {
            $table->id();
            $table->foreignId('council_id')->constrained('councils')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['council_id', 'department_id']);
            $table->index(['council_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('council_department');
    }
};
