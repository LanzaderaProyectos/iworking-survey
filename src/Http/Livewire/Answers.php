<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Exception;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Entry;
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

    public $disabled = false;


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


    public function mount(Entry $entry)
    {
        $this->entry = $entry;
        if($this->entry->status == Constants::ENTRY_STATUS_COMPLETED)
        {
            $this->disabled = true;
        }

        if ($this->entry->lang == 'en') {
            App::setlocale('en');
        }

        $this->survey = $this->entry->survey;
        $getAnswers                 =  AnswerService::setAnswersCommentsQuestions($this->entry);
        $this->answers              = $getAnswers['answers'];
        $this->comments             = $getAnswers['comments'];
        $this->respondedQuestions   = $getAnswers['respondedQuestions'];
        $this->professionalSelectOptions["treatments"] = config('iworking.user-treatment')::select('*')->orderBy('name','asc')->get();
        $userTypes = config('iworking.user-type')::select('*')->where('type','like','%-people')->orderBy('type','asc')->pluck('id')->toArray();
        $this->professionalsSurvey = config('iworking.user-model')::select('*')->orderBy('first_name','asc')->whereIn('type', $userTypes)->get();
        $this->initMultipleAnswers();
    }

    public function render()
    {
        return view('survey::livewire.answers');
    }

    public function updatedAnswers($value, $key)
    {
        $updatedQuestionId                              = explode('.', $key)[0];
        $this->respondedQuestions[$updatedQuestionId]   = $value;
        // dd($this->answers, $updatedQuestionId, $value, $this->entry, $this->answersToDelete);
        $answerService                                  = AnswerService::updatedAnswers($this->answers, $updatedQuestionId, $value, $this->entry, $this->answersToDelete);
        $this->answers                                  = $answerService['answers'];
        $this->answersToDelete                          = $answerService['answersToDelete'];
    }

    public function initMultipleAnswers()
    {
        foreach ($this->survey->surveyQuestions as $surveyQuestion) {
            if ($surveyQuestion->question->type == 'multiselect' && !isset($this->answers[$surveyQuestion->id]['value'])) {
                $this->answers[$surveyQuestion->id]['value'] = [];
                $this->answers[$surveyQuestion->id]['type'] = 'multiselect';
            }
        }
    }

    #[On('saveAnswers')]
    public function saveAnswers()
    {
        $saveAnswers = AnswerService::saveAnswers($this->answers, $this->entry, $this->comments);

        foreach ($this->answersToDelete as $key => $answer) {
            if ($answer->question->count() > 0) {
                $this->deleteSubQuestionAnswers($answer->question);
            }
            $answer->delete();
        }

        $this->answersToDelete  = [];
        $this->errorsBag        = [];
    }

    function deleteSubQuestionAnswers($question)
    {
        foreach ($question->subQuestions ?? [] as $subQuestion) {
            $subQuestion->answers()->where('entry_id', $this->entry->id)->get()->each->delete();
            if ($subQuestion->subQuestions->count() > 0) {
                $this->deleteSubQuestionAnswers($subQuestion);
            }
        }
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

    
    #[On('completeEntry')]
    public function completeEntry()
    {
        if($this->customValidation())
        {
            $this->saveAnswers();
            $this->dispatch('completedEntry');
        }
    }

    public function getSubQuestionsAfterAnswer($question)
    {
        $questionId     = $question->id;
        $questionAnswer = $this->answers[$questionId]['value'];
        $subQuestions   = $question->children->whereIn('condition', [$questionAnswer, '00']);
        return $subQuestions;
    }

    public function customValidation()
    {
        $answersService = AnswerService::answersCustomValidation($this->survey, $this->answers, $this->errorsBag, $this->comments);
        $this->errorsBag = $answersService['errorsBag'] ?? [];
        if (!$answersService['status']) {
            session()->flash('answersAlert', 'Hay preguntas obligatorias sin responder.');
        }
        return $answersService['status'];
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

    public function exportSurveyToPDF()
    {
        try {
            $name = "Formulario_" . $this->survey->survey_number . "_";;
            if ($this->survey->type == "pharmaciesSale") {
                $name .= "Venta_Farmacia";
            } elseif ($this->survey->type == "medicalPrescription") {
                $name .= "Prescripción_Médica";
            } elseif ($this->survey->type == "general") {
                $name .= "General";
            } else {
                $name .= "Formación";
            }

            $data = ['entry' => $this->entry, 'onlyOrder' => false, 'answers' => $this->answers];
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, "isPhpEnabled" => true])->loadView('exports.survey.pdf-survey', $data);
            $pdf = $pdf->output();
            return response()->streamDownload(
                fn () => print($pdf),
                $name . '.pdf'
            );
        } catch (Exception $e) {
            dd($data, $e->getMessage());
        }
    }

}
