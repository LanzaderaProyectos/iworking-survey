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
        if (!Schema::hasColumn('surveys', 'disabled_at')) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->timestamp('disabled_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('surveys', 'disabled_at')) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->dropColumn('disabled_at');
            });
        }
    }
};
