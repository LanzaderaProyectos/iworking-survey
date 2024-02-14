<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Answer;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Mail\SurveyCompleted;
use MattDaneshvar\Survey\Models\Question;

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
        $decrypted  =  Crypt::decryptString($crypted);
        $decrypted  = explode(';', $decrypted);

        // Pos [0] => email, Pos [1] => survey_id
        $this->entry = Entry::where('participant', $decrypted[0])
            ->where('survey_id', $decrypted[1])->first();
        if ($this->entry->lang == 'en') {
            App::setlocale('en');
        }
        $this->survey = Survey::find($this->entry->survey_id);

        $this->getAnswers();
    }

    public function getAnswers()
    {
        $answers = Answer::where('entry_id', $this->entry->id)
            ->get();


        foreach ($answers as $item) {
            $question                                                       = $item->question;
            $this->answers[$item->question_id]['value']                     = $item->value;
            $this->answers[$item->question_id]['comments']                  = $question->comments;
            $this->answers[$item->question_id]['type']                      = $question->type;
            $this->answers[$item->question_id]['question_parent_id']        = $question->parent_id;
            $this->answers[$item->question_id]['question_question_id']      = $question->original_id;
            $this->answers[$item->question_id]['model']                     = $item;
            $this->comments[$item->question_id]                             = $item->comments;
            if ($question->answers->count() > 0) {
                $this->respondedQuestions[$question->id] = true;
            }
        }
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
        $values = [
            'NO'                => 0,
            'SI'                => 100,
            'YES'               => 100,
            'NP'                => 100,
            'NA'                => 100,
            'Partially'         => 25,
            'Mainly'            => 70,
            'Totally'           => 100,
            'Parcialmente'      => 25,
            'Mayoritariamente'  => 70,
            'Totalmente'        => 100,
        ];

        foreach ($this->answers as $key => $answer) {
            $score = 0;
            if ($answer['type'] == 'radio' && $answer['value'] != '') {
                $score = $values[$answer['value']];
            }

            Answer::updateOrCreate(
                [
                    'question_id'   => $key,
                    'entry_id'      => $this->entry->id
                ],
                [
                    'value'     => $answer['value'],
                    'comments'  => $this->comments[$key] ?? null,
                    'score'     => $score
                ]
            );
        }

        foreach ($this->answersToDelete as $key => $value) {
            $value->delete();
        }

        $this->getAnswers();


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
