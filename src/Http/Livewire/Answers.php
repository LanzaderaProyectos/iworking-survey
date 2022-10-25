<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Answer;
use MattDaneshvar\Survey\Models\Survey;
use PHPUnit\TextUI\XmlConfiguration\Constant;

class Answers extends Component
{
    public $entry;
    public $survey;
    public $answers = [];
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
            $this->answers[$value->id] = '';
        }
        $answers = Answer::where('entry_id', $this->entry->id)
            ->get();
        foreach ($answers as $item) {
            $this->answers[$item->question_id] = $item->value;
        }
    }

    public function render()
    {
        return view('survey::livewire.answers');
    }

    public function saveAnswers()
    {
        foreach ($this->answers as $key => $value) {
            Answer::updateOrCreate(
                [
                    'question_id' => $key,
                    'entry_id' => $this->entry->id
                ],
                ['value' => $value,]
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
            return redirect('/');
        }
    }

    public function customValidation()
    {
        foreach ($this->answers as $key => $item) {
            if (empty(trim($item))) {
                $this->errorsBag[$key] = $key . "";
            } else {
                unset($this->errorsBag[$key]);
            }
        }
        if (empty($this->errorsBag)) {
            return true;
        }
        return false;
    }
}
