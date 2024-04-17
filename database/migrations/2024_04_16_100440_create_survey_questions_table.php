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
        Schema::create(config('survey.database.tables.survey_questions'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('question_id');
            $table->integer('survey_id');
            $table->integer('position');
            $table->integer('parent_id')->nullable();
            $table->integer('original_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->string('condition')->nullable();
            $table->boolean('mandatory');
            $table->integer('order');
            $table->boolean('disabled')->default(false);
            $table->uuid('disabled_by')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('survey.database.tables.survey_questions'));
    }
};
