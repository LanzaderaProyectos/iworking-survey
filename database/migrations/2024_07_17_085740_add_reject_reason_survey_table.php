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
        if (!Schema::hasColumn('surveys', 'reject_reason')) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->string('reject_reason')->after('original_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('surveys', 'reject_reason')) {
            Schema::table('surveys', function (Blueprint $table) {
                $table->dropColumn('reject_reason');
            });
        }
    }
};
