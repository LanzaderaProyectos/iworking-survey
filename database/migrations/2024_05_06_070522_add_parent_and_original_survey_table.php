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
        Schema::table(config('survey.database.tables.surveys'), function (Blueprint $table) {
            $table->integer('parent_id')->nullable()->after('expiration');
            $table->integer('original_id')->nullable()->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('survey.database.tables.surveys'), function (Blueprint $table) {
            $table->dropColumn(['parent_id', 'original_id']);
        });
    }
};
