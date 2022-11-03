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

class Answers extends Component
{
    public $entry;
    public $survey;
    public $answers = [];
    public $comments = [];
    public $errorsBag = [];

    public function mount()
    {
        $crypted = Route::current()->parameter('user');
        $decrypted =  Crypt::decryptString($crypted);
        $decrypted = explode(';', $decrypted);
        // Pos [0] => email, Pos [1] => survey_id
        $this->entry = Entry::where('participant', $decrypted[0])
            ->where('survey_id', $decrypted[1])->first();
        if ($this->entry->lang == 'en') {
            App::setlocale('en');
        }
        $this->survey = Survey::find($this->entry->survey_id);
        foreach ($this->survey->questions as  $value) {
            $this->answers[$value->id]['value'] = '';
            $this->answers[$value->id]['comments'] = $value->comments ?? '';
            $this->answers[$value->id]['type'] = $value->type;
            if ($value->comments) {
                $this->comments[$value->id] = '';
            }
        }
        $answers = Answer::where('entry_id', $this->entry->id)
            ->get();
        foreach ($answers as $item) {
            $this->answers[$item->question_id]['value'] = $item->value;
            $this->comments[$item->question_id] = $item->comments;
        }
    }

    public function render()
    {
        return view('survey::livewire.answers');
    }

    public function saveAnswers()
    {
        $values = [
            'NO' => 0,
            'SI' => 100,
            'YES' => 100,
            'NP' => 100,
            'NA' => 100,
            'Partially' => 25,
            'Mainly' => 70,
            'Totally' => 100,
            'Parcialmente' => 25,
            'Mayoritariamente' => 70,
            'TotaTotalmentelly' => 100,
        ];
        foreach ($this->answers as $key => $answer) {
            $score = 0;
            if ($answer['type'] == 'radio' && $answer['value'] != '') {
                $score = $values[$answer['value']];
            }
            Answer::updateOrCreate(
                [
                    'question_id' => $key,
                    'entry_id' => $this->entry->id
                ],
                [
                    'value' => $answer['value'],
                    'comments' => $this->comments[$key] ?? null,
                    'score' => $score
                ]
            );
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
                Mail::to($this->entry->participant)
                    ->send(new SurveyCompleted($this->entry));
            } catch (\Throwable $th) {
                Log::error($th);
            }
            return redirect('/');
        }
    }

    public function customValidation()
    {
        foreach ($this->answers as $key => $item) {
            if (empty(trim($item['value']))) {
                $this->errorsBag[$key] = $key . "";
            } elseif (trim($item['value'] == 'SI' && $item['comments'])) {
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
