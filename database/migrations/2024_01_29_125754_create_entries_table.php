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
        Schema::create(config('survey.database.tables.entries'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('survey_id');
            $table->string('participant');
            $table->string('lang')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('survey.database.tables.entries'));
    }
};
