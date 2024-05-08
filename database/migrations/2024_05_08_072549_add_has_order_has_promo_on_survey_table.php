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
    { if (!Schema::hasColumn(config('survey.database.tables.surveys'), 'has_order') && !Schema::hasColumn(config('survey.database.tables.surveys'), 'has_promotional_material')) {
        Schema::table(config('survey.database.tables.surveys'), function (Blueprint $table) {
            $table->boolean('has_order')->nullable()->after('parent_id');
            $table->boolean('has_promotional_material')->nullable()->after('has_order');
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('projects', 'has_order') && !Schema::hasColumn(config('survey.database.tables.surveys'), 'has_promotional_material')) {
        Schema::table(config('survey.database.tables.surveys'), function (Blueprint $table) {
            $table->dropColumn(['has_order', 'has_promotional_material']);
        });
    }
    }
};
