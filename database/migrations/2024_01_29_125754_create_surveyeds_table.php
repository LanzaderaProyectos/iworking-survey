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
        Schema::create('surveyeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('survey_id');
            $table->string('name');
            $table->string('vat_number')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email');
            $table->string('lang');
            $table->string('manager')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveyeds');
    }
};
