<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Http\Controllers\Api\Process\SurveyController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/


Route::post('integration/{publicApiKey}/process/survey/{survey}/task/assignee',   [SurveyController::class, 'userAssignee']);
Route::post('integration/{publicApiKey}/process/survey/{survey}/set/status',      [SurveyController::class, 'setStatus']);
Route::post('integration/{publicApiKey}/process/survey/{survey}/set/audit',       [SurveyController::class, 'setAudit']);

