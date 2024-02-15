<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Mail\SurveyCompleted;
use MattDaneshvar\Survey\Facades\AnswerService;
use MattDaneshvar\Survey\Services\DecryptionService;

class Answers extends Component
{
    public $entry;
    public $survey;
    public $answers             = [];
    public $comments            = [];
    public $errorsBag           = [];
    public $respondedQuestions  = [];
    public $answersToDelete     = [];

    public function mount()
    {
        $crypted    = Route::current()->parameter('user');
        $this->entry = (new DecryptionService())->decryptUser($crypted);

        if ($this->entry->lang == 'en') {
            App::setlocale('en');
        }

        $this->survey = $this->entry->survey;

        $getAnswers                 =  AnswerService::setAnswersCommentsQuestions($this->entry);
        $this->answers              = $getAnswers['answers'];
        $this->comments             = $getAnswers['comments'];
        $this->respondedQuestions   = $getAnswers['respondedQuestions'];
    }

    public function render()
    {
        return view('survey::livewire.answers');
    }

    public function updatedAnswers($value, $key)
    {
        $updatedQuestionId                              = explode('.', $key)[0];
        $this->respondedQuestions[$updatedQuestionId]   = $value;
        $answerService                                  = AnswerService::updatedAnswers($this->answers, $updatedQuestionId, $value, $this->entry, $this->answersToDelete);
        $this->answers                                  = $answerService['answers'];
        $this->answersToDelete                          = $answerService['answersToDelete'];
    }

    public function saveAnswers()
    {
        $saveAnswers = AnswerService::saveAnswers($this->answers, $this->entry, $this->comments);

        foreach ($this->answersToDelete as $key => $value) {
            $value->delete();
        }

        $this->errorsBag = [];
    }

    public function sendAnswers()
    {
        $this->saveAnswers();
        
        if ($this->customValidation()) {
            $this->entry->status = Constants::ENTRY_STATUS_COMPLETED;
            $this->entry->save();
            try {
                Mail::mailer('custom')->to($this->entry->participant)
                    ->send(new SurveyCompleted($this->entry));
            } catch (\Throwable $th) {
                Log::error($th);
            }
            return redirect('/');
        }
    }

    public function getSubQuestionsAfterAnswer($question)
    {
        $questionId     = $question->id;
        $questionAnswer = $this->answers[$questionId]['value'];
        $subQuestions   = $question->subQuestions->where('condition', $questionAnswer);

        return $subQuestions;
    }

    public function customValidation()
    {
        $answersService = AnswerService::answersCustomValidation($this->answers, $this->errorsBag, $this->comments);

        if (!$answersService['status']) {
            session()->flash('answersAlert', 'Hay preguntas sin responder.');
        }else{
            $this->errorsBag = $answersService['errorsBag'];
            $this->comments  = $answersService['comments'];
            $this->answers   = $answersService['answers'];
        }

        return $answersService['status'];
    }
}
