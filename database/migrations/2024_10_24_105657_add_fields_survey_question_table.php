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
        if (!Schema::hasColumn('survey_questions', 'chart_type')) {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->string('chart_type')->after('target_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('survey_questions', 'chart_type')) {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->dropColumn('chart_type');
            });
        }
    }
};
