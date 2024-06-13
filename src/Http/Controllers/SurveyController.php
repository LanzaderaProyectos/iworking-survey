<?php
namespace MattDaneshvar\Survey\Http\Controllers;

use App\Http\Controllers\Controller;
use Iworking\IworkingProcesses\Facades\TaskService;

class SurveyController extends Controller
{
    public function open(
        $processName,
        $processId          = null,
        $processInstance    = null,
        $processActivity    = null,
    ) {
        $task = TaskService::validateTaskOpen($processInstance, auth()->user()->id, true);

        if (!$task) {
            return redirect()->route('dashboard');
        }

        return view('survey::surveys.task', [
            'surveyId'  => $processId,
            'taskId'    => $processInstance,
        ]);

    }
}