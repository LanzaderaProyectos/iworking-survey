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
        if (!Schema::hasColumn('entries', 'participant_type')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->string('participant_type')->nullable();
                $table->uuid('participant')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('entries', 'participant_type')) {
            Schema::table('entries', function (Blueprint $table) {
                $table->dropColumn('participant_type');
                $table->string('participant')->change();
            });
        }
    }
};
