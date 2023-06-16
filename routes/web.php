<?php

// use Illuminate\Support\Facades\Route;
Route::name('survey.')->group(function () {
    Route::middleware(config('iworking-survey.middleware'))->group(function () {
        // Route::view('provider-portal/task-list', 'processes.task-list')->name('task-list');
        Route::view('survey/list', 'survey::surveys.survey-list')->name('list');
        Route::view('survey/new', 'survey::surveys.form')->name('new');
        Route::view('survey/show/{surveyId}', 'survey::surveys.form')->name('show');
        Route::view('survey/{surveyId}', 'survey::surveys.form')->name('edit');
        Route::view('survey/entry/{entryId}', 'survey::surveys.entry')->name('entry');
        Route::view('survey/entries/{surveyId}', 'survey::surveys.entry-list')->name('entry.list');
    });
    Route::view('survey/answers/{user}', 'survey::surveys.answers')
        ->name('answers')
        ->middleware(['web', 'user-survey']);
    Route::view('surveys/survey-not-available', 'survey::survey-not-available')->name('not-available');
});