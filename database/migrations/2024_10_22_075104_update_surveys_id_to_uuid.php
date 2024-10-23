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
        Schema::table(config('survey.database.tables.answers'), function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('question_id')->change();
            $table->uuid('entry_id')->change();
        });
        Schema::table(config('survey.database.tables.entries'), function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('survey_id')->change();
        });
        Schema::table(config('survey.database.tables.questions'), function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('original_id')->change();
            $table->uuid('parent_id')->change();
            $table->uuid('survey_id')->change();
            $table->uuid('section_id')->change();
        });
        Schema::table(config('survey.database.tables.sections'), function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('survey_id')->change();
        });
        Schema::table(config('survey.database.tables.survey_questions'), function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('question_id')->change();
            $table->uuid('survey_id')->change();
            $table->uuid('parent_id')->change();
            $table->uuid('original_id')->change();
            $table->uuid('section_id')->change();
        });
        Schema::table(config('survey.database.tables.surveys'), function (Blueprint $table) {
            $table->uuid('id')->change();
            $table->uuid('parent_id')->change();
            $table->uuid('original_id')->change();
        });
        Schema::table(config('survey.database.tables.survey_types'), function (Blueprint $table) {
            $table->uuid('id')->change();
        });
        /*
            Schema::table('project_surveys', function (Blueprint $table) {
                $table->uuid('survey_id')->change();
            });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('survey.database.tables.answers'), function (Blueprint $table) {
            $table->id('id')->change();
            $table->integer('question_id')->change();
            $table->integer('entry_id')->change();
        });
        Schema::table(config('survey.database.tables.entries'), function (Blueprint $table) {
            $table->id('id')->change();
            $table->integer('survey_id')->change();
        });
        Schema::table(config('survey.database.tables.questions'), function (Blueprint $table) {
            $table->id('id')->change();
            $table->integer('original_id')->change();
            $table->integer('parent_id')->change();
            $table->integer('survey_id')->change();
            $table->integer('section_id')->change();
        });
        Schema::table(config('survey.database.tables.sections'), function (Blueprint $table) {
            $table->id('id')->change();
            $table->integer('survey_id')->change();
        });
        Schema::table(config('survey.database.tables.survey_questions'), function (Blueprint $table) {
            $table->id('id')->change();
            $table->integer('question_id')->change();
            $table->integer('survey_id')->change();
            $table->integer('parent_id')->change();
            $table->integer('original_id')->change();
            $table->integer('section_id')->change();
        });
        Schema::table(config('survey.database.tables.surveys'), function (Blueprint $table) {
            $table->id('id')->change();
            $table->integer('parent_id')->change();
            $table->integer('original_id')->change();
        });
        Schema::table(config('survey.database.tables.survey_types'), function (Blueprint $table) {
            $table->id('id')->change();
        });
    }
};
