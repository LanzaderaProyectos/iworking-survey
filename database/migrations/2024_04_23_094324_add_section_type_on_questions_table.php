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
            $table->date('disabled_at')->nullable()->after('disabled');
            $table->string('section_type')->nullable()->after('survey_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('survey.database.tables.questions'), function (Blueprint $table) {
            $table->dropColumn(['section_type','disabled_at']);
        });
    }
};
