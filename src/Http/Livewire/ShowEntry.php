<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Models\Answer;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use PHPUnit\TextUI\XmlConfiguration\Constant;

class ShowEntry extends Component
{
    public $entry;
    public $survey;
    public $answers = [];
    public $lang;

    public function mount()
    {
        $entryId = Route::current()->parameter('entryId');

        $this->entry = Entry::findOrFail($entryId);
        $this->survey = $this->entry->survey;
        $this->lang = $this->entry->lang;
        //Create array answers from questions
        foreach ($this->survey->questions as  $value) {
            $this->answers[$value->id] = '';
        }
        //Get answers value
        $answers = Answer::where('entry_id', $this->entry->id)
            ->get();
        foreach ($answers as $item) {
            $this->answers[$item->question_id] = $item->value;
        }
    }

    public function render()
    {
        return view('survey::livewire.show-entry', [
            'disabled' => true
        ]);
    }
}
