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
        if (!Schema::hasColumn('survey_questions', 'target_id')) {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->uuid('target_id')->after('target')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('survey_questions', 'target_id')) {
            Schema::table('survey_questions', function (Blueprint $table) {
                $table->dropColumn('target_id');
            });
        }
    }
};
