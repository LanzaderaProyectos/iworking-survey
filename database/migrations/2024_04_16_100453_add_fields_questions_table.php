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
        Schema::table(config('survey.database.tables.questions'), function (Blueprint $table) {
            $table->boolean('mandatory')->nullable()->after('rules');
            $table->string('survey_type')->nullable()->after('mandatory');
            $table->string('code')->nullable()->after('survey_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('survey.database.tables.questions'), function (Blueprint $table) {
            $table->dropColumn(['mandatory', 'survey_type', 'code']);
        });
    }
};
