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
        $question                                                       = Question::find($updatedQuestionId);

        $this->answers[$updatedQuestionId]['value']                     = $value['value'] ?? $value;
        $this->answers[$updatedQuestionId]['comments']                  = $question->comments;
        $this->answers[$updatedQuestionId]['type']                      = $question->type;
        $this->answers[$updatedQuestionId]['question_parent_id']        = $question->parent_id;
        $this->answers[$updatedQuestionId]['question_original_id']      = $question->original_id;
        $this->answers[$updatedQuestionId]['model']                     = $this->answers[$updatedQuestionId]['model'] ?? null;

        $subQuestions                                                   = $question->subQuestions;


        foreach ($subQuestions as $key => $subQuestion) {
            $id = $subQuestion->id;

            // Si existe la respuesta
            if (isset($this->answers[$id])) {

                // Si la respuesta es diferente a la condición
                if ($this->answers[$updatedQuestionId]['value'] != $subQuestion->condition) {
                    $answerToDelete = $subQuestion->answers()->where('entry_id', $this->entry->id)->first();
                    if ($answerToDelete) {
                        $this->answersToDelete[$id] = $answerToDelete;
                    }
                    unset($this->answers[$id]);
                } else {
                    // Si la respuesta es igual a la condición

                    // Si existe esa respuesta en el array de respuestas a eliminar
                    if (isset($this->answersToDelete[$id])) {
                        $question                                           = $this->answersToDelete[$id]->question;
                        $this->answers[$id]['value']                        = $value['value'] ?? $value;
                        $this->answers[$id]['comments']                     = $question->comments;
                        $this->answers[$id]['type']                         = $question->type;
                        $this->answers[$id]['question_parent_id']           = $question->parent_id;
                        $this->answers[$id]['question_original_id']         = $question->original_id;
                        $this->answers[$id]['model']                        = $this->answersToDelete[$updatedQuestionId];
                        unset($this->answersToDelete[$id]);
                    }
                }
            } else {
                if ($this->answers[$updatedQuestionId]['value'] == $subQuestion->condition) {
                    $this->answers[$id]['value']                    = '';
                    $this->answers[$id]['comments']                 = $question->comments;
                    $this->answers[$id]['type']                     = $question->type;
                    $this->answers[$id]['question_parent_id']       = $question->parent_id;
                    $this->answers[$id]['question_original_id']     = $question->original_id;
                }
            }
        }
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
        foreach ($this->answers as $key => $item) {
            if (empty(trim($item['value']))) {
                $this->errorsBag[$key] = $key . "";
            } elseif ((trim($item['value'] == 'SI' || trim($item['value'] == 'YES')) && $item['comments'])) {
                if (empty(trim($this->comments[$key]))) {
                    $this->errorsBag[$key] = $key . "";
                }
            } else {
                unset($this->errorsBag[$key]);
            }
        }
        if (empty($this->errorsBag)) {
            return true;
        }
        session()->flash('answersAlert', 'Hay preguntas sin responder.');
        return false;
    }

    
}
