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
        if (!Schema::hasColumn(config('survey.database.tables.survey_questions'), 'indicated') && !Schema::hasColumn(config('survey.database.tables.survey_questions'), 'target')) {
            Schema::table(config('survey.database.tables.survey_questions'), function (Blueprint $table) {
                $table->boolean('indicated')->nullable()->after('mandatory');
                $table->boolean('target')->nullable()->after('indicated');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn(config('survey.database.tables.survey_questions'), 'indicated') && !Schema::hasColumn(config('survey.database.tables.survey_questions'), 'has_promotional_material')) {
            Schema::table(config('survey.database.tables.survey_questions'), function (Blueprint $table) {
                $table->dropColumn(['indicated', 'target']);
            });
        }
    }
};
