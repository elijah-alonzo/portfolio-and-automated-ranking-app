<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluation_forms', function (Blueprint $table) {
            $table->dropUnique('evaluation_forms_evaluation_id_user_id_evaluator_type_unique');
            $table->unique(
                ['evaluation_id', 'user_id', 'evaluator_type', 'evaluator_id'],
                'evaluation_forms_eval_user_type_evalr_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('evaluation_forms', function (Blueprint $table) {
            $table->dropUnique('evaluation_forms_eval_user_type_evalr_unique');
            $table->unique(
                ['evaluation_id', 'user_id', 'evaluator_type'],
                'evaluation_forms_evaluation_id_user_id_evaluator_type_unique'
            );
        });
    }
};
