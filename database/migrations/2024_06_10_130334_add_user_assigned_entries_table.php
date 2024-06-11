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
        if (!Schema::hasColumn('entries', 'assigned_user_id')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->uuid('assigned_user_id')->after('status')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('entries', 'assigned_user_id')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->dropColumn('assigned_user_id');
            });
        }
    }
};
