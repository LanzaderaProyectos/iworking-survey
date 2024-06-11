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
        if (!Schema::hasColumn('entries', 'date_responded')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->date('date_responded')->after('assigned_user_id')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('entries', 'date_responded')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->dropColumn('date_responded');
            });
        }
    }
};
