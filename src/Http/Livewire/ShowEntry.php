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
    public $comments = [];
    public $lang;

    public $selectedProfessional;
    public $selectedProfessionalId;
    public $professionalsSurvey;
    public $professionalSelectOptions = [];

    protected $rules = [
        //user Selected
        'selectedProfessional.*'                            => 'nullable',
        'selectedProfessional.first_name'                   => 'nullable',
        'selectedProfessional.last_name'                    => 'nullable',
        'selectedProfessional.nif'                          => 'nullable',
        'selectedProfessional.job_title_id'                 => 'nullable',
        'selectedProfessional.prefix_phone'                 => 'nullable',
        'selectedProfessional.phone'                        => 'nullable',
        'selectedProfessional.prefix_mobile'                => 'nullable',
        'selectedProfessional.mobile_phone'                       => 'nullable',
        'selectedProfessional.mail_contact'                 => 'nullable',
        'selectedProfessional.other_contact_information'    => 'nullable',
        'selectedProfessional.consent_request'              => 'nullable',
        'selectedProfessional.consent'                      => 'nullable'
    ];

    public function mount()
    {
        $entryId = Route::current()->parameter('entryId');

        $this->entry = Entry::findOrFail($entryId);
        $this->survey = $this->entry->survey;
        $this->lang = $this->entry->lang;
        //Create array answers from questions
        foreach ($this->survey->questions as  $value) {
            $this->answers[$value->id]['value'] = '';
            $this->answers[$value->id]['type'] = $value->type;
            if ($value->comments) {
                $this->comments[$value->id] = '';
            }
        }
        $this->professionalSelectOptions["treatments"] = config('iworking.user-treatment')::select('*')->orderBy('name','asc')->get();
        $userTypes = config('iworking.user-type')::select('*')->where('type','like','%-people')->orderBy('type','asc')->pluck('id')->toArray();
        $this->professionalsSurvey = config('iworking.user-model')::select('*')->orderBy('first_name','asc')->whereIn('type', $userTypes)->get();
        //Get answers value
        $answers = Answer::where('entry_id', $this->entry->id)
            ->get();
        foreach ($answers as $item) {
            $this->answers[$item->question_id]['value'] = json_decode($item->value,true) ?? $item->value;
            $this->comments[$item->question_id] = $item->comments;
        }
    }

    public function render()
    {
        return view('survey::livewire.show-entry', [
            'disabled' => true
        ]);
    }

    
    public function updatedSelectedProfessionalId()
    {
        $this->selectedProfessional = config('iworking.user-model')::find($this->selectedProfessionalId);
        if($this->selectedProfessional)
        {
            $this->professionalSelectOptions["jobTitles"] = config('iworking.job-titles')::select('*')->where('user_type_id',$this->selectedProfessional->type)->orderBy('name','asc')->get();
        }
        else{
            $this->professionalSelectOptions["jobTitles"] = [];
        }
        
    }
}
