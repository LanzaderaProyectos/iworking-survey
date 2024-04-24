<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\User;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Section;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Models\Surveyed;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Models\SurveyQuestion;
use MattDaneshvar\Survey\Services\SurveyService;
use MattDaneshvar\Survey\Services\SectionService;
use MattDaneshvar\Survey\Services\QuestionService;
use MattDaneshvar\Survey\Mail\ReminderNotification;

class CreateSurvey extends Component
{
    public $survey      = null;
    public $surveyName  = [
        'es'    => '',
        'en'    => ''
    ];
    public $section     = null;
    public $sectionName = [
        'es'    => '',
        'en'    => ''
    ];
    public $question        = null;
    public $questionName    = [
        'es'    => '',
        'en'    => ''
    ];
    public $subQuestion     = null;
    public $subQuestionName = [
        'es'    => '',
        'en'    => ''
    ];
    public $surveyQuestion  = null;
    public $subSurveyQuestion  = null;
    public $users           = [];
    public $typeSelected    = null;
    public $subTypeSelected = null;
    public $newSurvey       = true;
    public $draft;
    public $typeAnwers = [
        'radio' => 'Si/No/NP',
        'multiselect' => 'Selección múltiple',
        'uniqueselect' => 'Selección única',
        'date' => 'Fecha',
        'hour' => 'Hora',
        'text' => 'Texto',
        'longText' => 'Texto largo',
        'number' => 'Númerico',
        'currency' => 'Moneda',
        // 'number' => 'Numero'
    ];
    public $subTypeAnwers = [
        'radio' => 'Si/No/NP',
        'multiselect' => 'Selección múltiple',
        'uniqueselect' => 'Selección única',
        'date' => 'Fecha',
        'hour' => 'Hora',
        'text' => 'Texto',
        'longText' => 'Texto largo',
        'number' => 'Númerico',
        'currency' => 'Moneda',
        // 'number' => 'Numero'
    ];
    public $editModeQuestion    = false;
    public $subEditModeQuestion = false;
    public $formEdit;
    public $optionES = [];
    public $optionEN = [];
    public $newOptionES = "";
    public $newOptionEN = "";
    public $customOptions = false;
    public $subOptionEs = [];
    public $subOptionEn = [];
    public $customSubOptions = false;
    public $updateOption = null;
    public $sectionQuestionSelected;
    public $orderQuestion;
    public $requiredQuestion = false;
    public $orderSubQuestion;
    public $requiredSubQuestion = false;
    public $defaultQuestions;
    public $defaultQuestionsSub;

    public $selectedParentQuestionId;
    public $selectedParentQuestion;
    public $selectedDefaultQuestion;
    public $selectedDefaultQuestionOrder;
    public $selectedDefaultQuestionSub;
    public $parentQuestionRadio;

    public $selectedProfessional;
    public $selectedProfessionalId;
    public $professionalsSurvey;
    public $professionalSelectOptions = [];


    protected $rules = [
        //Survey
        'survey.expiration'     => 'required',
        'survey.comments'       => 'nullable',
        'survey.type'           => 'nullable',
        'survey.project_type'   => 'nullable',
        'surveyName.es'         => 'required',
        'surveyName.en'         => 'nullable',
        //Sections
        'sectionName.es'        => 'nullable',
        'sectionName.en'        => 'nullable',
        'section.order'         => 'nullable',
        //Questions
        'questionName.es'       => 'nullable',
        'questionName.en'       => 'nullable',
        'question.section_id'   => 'nullable',
        'question.order'        => 'nullable',
        'question.comments'     => 'nullable',
        //SubQuestion

        'subQuestionName.es'       => 'nullable',
        'subQuestionName.en'       => 'nullable',
        'subQuestion.section_id'   => 'nullable',
        'subQuestion.order'        => 'nullable',
        'subQuestion.comments'     => 'nullable',

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

    public function mount($draft = false)
    {
        $this->draft            = $draft;
        $this->formEdit         = !Route::is('survey.show');
        $this->initComponent();
        $this->defaultQuestions = (new QuestionService())->getDefaultQuestions($this->survey->type ?? '');
        $this->professionalSelectOptions["treatments"] = config('iworking.user-treatment')::select('*')->orderBy('name', 'asc')->get();
        $userTypes = config('iworking.user-type')::select('*')->where('type', 'like', '%-people')->orderBy('type', 'asc')->pluck('id')->toArray();
        $this->professionalsSurvey = config('iworking.user-model')::select('*')->orderBy('first_name', 'asc')->whereIn('type', $userTypes)->get();
        if($this->survey->sections()->where('name','like','%General%')->exists())
        {
            $this->sectionQuestionSelected = $this->survey->sections()->where('name','like','%General%')->first()->id;
        }
    }

    public function initComponent()
    {
        $surveyId = Route::current()->parameter('surveyId');
        if ($surveyId) {
            $this->survey               = Survey::find($surveyId);
            $this->surveyName['es']     = $this->survey->getTranslation('name', 'es');
            $this->surveyName['en']     = $this->survey->getTranslation('name', 'en');
            $this->newSurvey            = false;
            $this->question             = new Question();
            $this->subQuestion          = new Question();
            $this->section              = new Section();
            $this->surveyQuestion       = new SurveyQuestion();
            $this->subSurveyQuestion    = new SurveyQuestion();
        } else {
            $this->survey = new Survey();
        }
        $this->optionES = [
            'SI',
            'NO',
            'NP'
        ];
        $this->optionEN = [
            'YES',
            'NO',
            'NA'
        ];
    }

    public function render()
    {
        return view('survey::livewire.create-survey');
    }



    public function addOption()
    {
        $this->validate([
            'newOptionES' => 'required',
            'newOptionEN' => 'required',
        ]);

        if ($this->updateOption != null) {
            $this->optionES[$this->updateOption] = $this->newOptionES;
            $this->optionEN[$this->updateOption] = $this->newOptionEN;
        } else {
            $this->optionES[] = $this->newOptionES;
            $this->optionEN[] = $this->newOptionEN;
        }

        $this->newOptionES = "";
        $this->newOptionEN = "";
        $this->updateOption = null;
    }

    public function deleteOption($position)
    {
        unset($this->optionES[$position]);
        unset($this->optionEN[$position]);
    }

    public function editOption($id)
    {
        $this->newOptionES = $this->optionES[$id];
        $this->newOptionEN = $this->optionEN[$id];
        $this->updateOption = $id;
    }

    public function saveSurvey()
    {
        $this->validate();
        $surveyService          = new SurveyService();
        $result                 = $surveyService->saveSurvey(
            survey: $this->survey,
            authorId: auth()->user()->id,
            surveyName: $this->surveyName,
            surveyStatus: Constants::SURVEY_STATUS_DRAFT,
            auditText: 'Formulario creado',
            newSurvey: $this->newSurvey
        );

        if ($result) {

            if ($this->newSurvey) {
                $this->createSections();
                session()->flash('draftSurveyCreated', 'Formulario creado');
                return redirect(route('survey.edit', [
                    'surveyId' => $this->survey->id
                ]));
            }
            session()->flash('surveyUpdated', 'Cambios guardados');
        } else {
            session()->flash('surveyUpdated', 'Error al guardar los cambios');
        }
    }

    public function deleteSurvey()
    {
        $this->survey->sections()->delete();
        $this->survey->surveyQuestions()->delete();
        $this->survey->delete();
        session()->flash('surveyDeleted', 'Formulario eliminado');

        return redirect(route('survey.list'));
    }

    public function createSections()
    {
        if ($this->survey->type == "general") {
            $this->section = new Section();
            $this->sectionName['es'] = "General";
            $this->sectionName['en'] = "General";
            $this->section->order = 1;
            $this->addSection();
        } elseif ($this->survey->type == "pharmaciesSale") {
            $this->section = new Section();
            $this->sectionName['es'] = "General";
            $this->sectionName['en'] = "General";
            $this->section->order = 1;
            $this->addSection();
        } elseif ($this->survey->type == "medicalPrescription") {
            $this->section = new Section();
            $this->sectionName['es'] = "General";
            $this->sectionName['en'] = "General";
            $this->section->order = 1;
            $this->addSection();
            $this->section = new Section();
            $this->sectionName['es'] = "Preguntas";
            $this->sectionName['en'] = "Questions";
            $this->section->order = 2;
            $this->addSection();
        } else {
            $this->section = new Section();
            $this->sectionName['es'] = "General";
            $this->sectionName['en'] = "General";
            $this->section->order = 1;
            $this->addSection();
            $this->section = new Section();
            $this->sectionName['es'] = "Agendar Formación";
            $this->sectionName['en'] = "Agendar Formación";
            $this->section->order = 2;
            $this->addSection();
            $this->section = new Section();
            $this->sectionName['es'] = "Formación realizada";
            $this->sectionName['en'] = "Formación realizada";
            $this->section->order = 3;
            $this->addSection();
        }
    }

    public function sendSurvey()
    {
        // $this->users = Surveyed::where('survey_id', $this->survey->id)->get();
        // if (!$this->users->count()) {
        //     session()->flash('userListEmpty', 'No hay usuarios');
        //     return;
        // }

        // $surveyService  = new SurveyService();
        // $result         = $surveyService->sendSurvey(
        //     users: $this->users,
        //     survey: $this->survey,
        //     audit: true,
        //     auditText: 'Formulario enviado'
        // );

        // if ($result) {
        //     session()->flash('surveySended',  'Formulario enviado');
        //     return redirect(route('survey.list'));
        // } else {
        //     session()->flash('surveySended',  'Error al enviar el Formulario');
        // }
    }

    public function addSection()
    {
        $this->validate([
            'sectionName.es' => 'required',
            'sectionName.en' => 'nullable',
            'section.order' =>  'nullable|numeric'
        ]);

        $sectionService = new SectionService();
        $result         = $sectionService->saveSection(
            section: $this->section,
            surveyId: $this->survey->id,
            sectionName: $this->sectionName
        );

        if ($result) {
            $this->reset('sectionName');
            $this->section = new Section();
            $this->survey->refresh();
        } else {
            session()->flash('sectionSaved', 'Error al guardar la sección');
        }
    }

    public function deleteSection($id)
    {
        $section = Section::find($id);
        $section->questions()->delete();
        $section->delete();
        $this->survey->refresh();
    }

    public function saveQuestion()
    {
        $this->validate([
            'orderQuestion'               => 'required',
            'sectionQuestionSelected'     => 'required',
        ]);
        // $this->question->update(['original_id' => $this->question->id]);
        if (empty($this->surveyQuestion->id)) {
            if (!SurveyQuestion::where('survey_id', $this->survey->id)->where('question_id', $this->question->id)->whereNull('parent_id')->exists()) {
                $this->surveyQuestion->survey_id = $this->survey->id;
                $this->surveyQuestion->question_id = $this->question->id;
                $this->surveyQuestion->order = $this->orderQuestion;
                $this->surveyQuestion->position = $this->orderQuestion;
                $this->surveyQuestion->section_id = $this->sectionQuestionSelected;
                $this->surveyQuestion->mandatory = $this->requiredQuestion;
                $this->surveyQuestion->disabled = false;
                $this->surveyQuestion->save();
                $this->surveyQuestion->update(['original_id' => $this->surveyQuestion->id]);
                $this->surveyQuestion = new SurveyQuestion();
            } else {
                session()->flash('questionWarning', 'La pregunta ya existe en el formulario');
                return;
            }
        } else {
            $this->surveyQuestion = SurveyQuestion::where('survey_id', $this->survey->id)->where('question_id', $this->question->id)->first();
            $this->surveyQuestion->update(['position' => $this->orderQuestion]);
            $this->surveyQuestion->update(['section_id' => $this->sectionQuestionSelected]);
            $this->surveyQuestion->update(['mandatory' => $this->requiredQuestion]);
        }
        $this->reset('questionName');
        $this->resetValues();
        session()->flash('questionSaved', 'Pregunta guardada');
        $this->survey->refresh();
        $this->optionEN = [];
        $this->optionES = [];
        $this->customOptions = false;
        $this->updateOption = null;
    }

    public function saveSubQuestion()
    {

        $this->validate([
            'subQuestionName.es'       => 'required',
            'subQuestionName.en'       => 'nullable',
            'orderSubQuestion'        => 'required|numeric',
        ]);
        if (empty($this->subSurveyQuestion->id)) {
            if (!SurveyQuestion::where('survey_id', $this->survey->id)->where('question_id', $this->subQuestion->id)->where('parent_id', $this->selectedDefaultQuestionSub)->exists()) {
                $surveyQuestionParent = SurveyQuestion::find($this->selectedParentQuestionId);
                $this->subSurveyQuestion->survey_id = $this->survey->id;
                $this->subSurveyQuestion->question_id = $this->subQuestion->id;
                $this->subSurveyQuestion->order = (int)$this->orderSubQuestion ?? 0;
                $this->subSurveyQuestion->section_id = $surveyQuestionParent->section_id;
                $this->subSurveyQuestion->mandatory = $this->requiredSubQuestion;
                $this->subSurveyQuestion->disabled = false;
                $this->subSurveyQuestion->parent_id = $surveyQuestionParent->id;
                $this->subSurveyQuestion->condition = $this->parentQuestionRadio;
                $this->subSurveyQuestion->original_id = $surveyQuestionParent->original_id;
                $this->subSurveyQuestion->save();
                $this->subSurveyQuestion = new SurveyQuestion();
            } else {
                session()->flash('questionWarning', 'La pregunta ya existe en el formulario');
                return;
            }
        } else {
            $this->subSurveyQuestion = SurveyQuestion::where('survey_id', $this->survey->id)->where('question_id', $this->subQuestion->id)->where('parent_id', $this->selectedParentQuestionId)->first();
            $this->subSurveyQuestion->update(['position' => $this->orderSubQuestion]);
            $this->subSurveyQuestion->update(['mandatory' => $this->requiredSubQuestion]);
        }
        $this->reset('questionName');
        $this->resetValues();
        session()->flash('questionSaved', 'Pregunta guardada');
        $this->survey->refresh();
        $this->optionEN = [];
        $this->optionES = [];
        $this->customOptions = false;
        $this->updateOption = null;

        $this->subQuestion = new Question();
    }

    public function addDefaultQuestion()
    {
        try {
            $this->validate([
                'selectedDefaultQuestion'       => 'required',
                'selectedDefaultQuestionOrder'  => 'nullable|numeric',
            ], [
                'selectedDefaultQuestion.required' => 'Seleccione una pregunta por defecto',
            ]);
            if (!SurveyQuestion::where('survey_id', $this->survey->id)->where('question_id', $this->selectedDefaultQuestion)->whereNull('parent_id')->exists()) {
                $this->question                     = Question::find($this->selectedDefaultQuestion);
                $this->questionName['es']           = $this->question->getTranslation('content', 'es');
                $this->questionName['en']           = $this->question->getTranslation('content', 'en');
                $this->typeSelected                 = $this->question->type;
                $this->surveyQuestion              = new SurveyQuestion();
                if ($this->typeSelected == "multiselect" || $this->typeSelected == "uniqueselect") {
                    $this->customOptions = true;
                    if ((is_array($this->question->options) && !empty($this->question->options)) || !empty(json_decode($this->question->options ?? '', true) ?? [])) {
                        $this->optionES = $this->question->getTranslation('options', 'es');
                        $this->optionEN = $this->question->getTranslation('options', 'en');
                    } else {
                        $this->optionES = [];
                        $this->optionEN = [];
                    }
                }
            } else {
                session()->flash('questionWarning', 'La pregunta ya existe en el formulario');
                return;
            }
        } catch (\Throwable $th) {
            session()->flash('alert', $th->getMessage());
        }
    }

    public function addDefaultQuestionSub()
    {
        try {
            $this->validate([
                'selectedDefaultQuestionSub'       => 'required',
            ], [
                'selectedDefaultQuestionSub.required' => 'Seleccione una pregunta por defecto',
            ]);
            if (!SurveyQuestion::where('id', $this->selectedDefaultQuestionSub)->where('parent_id', $this->selectedParentQuestionId)->exists()) {
                $this->subQuestion                     = Question::find($this->selectedDefaultQuestionSub);
                $this->subQuestionName['es']           = $this->subQuestion->getTranslation('content', 'es');
                $this->subQuestionName['en']           = $this->subQuestion->getTranslation('content', 'en');
                $this->subTypeSelected                 = $this->subQuestion->type;
                if ($this->subTypeSelected == "multiselect" || $this->subTypeSelected == "uniqueselect") {
                    $this->customSubOptions = true;
                    $this->subOptionEs = $this->subQuestion->getTranslation('options', 'es');
                    $this->subOptionEn = $this->subQuestion->getTranslation('options', 'en');
                }
            } else {
                session()->flash('questionWarning', 'La pregunta ya existe en el formulario');
                return;
            }
            $this->subSurveyQuestion = new SurveyQuestion();
        } catch (\Throwable $th) {
            session()->flash('alert', $th->getMessage());
        }
    }

    public function editQuestion($id, $subQuestion = false)
    {

        if (!$subQuestion) {
            $this->surveyQuestion           = SurveyQuestion::find($id);
            $this->question                 = $this->surveyQuestion->question;
            $this->orderQuestion            = $this->surveyQuestion->position;
            $this->sectionQuestionSelected  = $this->surveyQuestion->section_id;
            $this->requiredQuestion         = $this->surveyQuestion->mandatory;
            $this->typeSelected             = $this->question->type;
            $this->questionName['es']       = $this->question->getTranslation('content', 'es');
            $this->questionName['en']       = $this->question->getTranslation('content', 'en');
            $this->editModeQuestion         = true;
            if ($this->typeSelected == "multiselect" || $this->typeSelected == "uniqueselect") {
                $this->customOptions = true;
                $this->optionES = $this->question->getTranslation('options', 'es');
                $this->optionEN = $this->question->getTranslation('options', 'en');
            }
        } else {
            $this->subSurveyQuestion           = SurveyQuestion::find($id);
            $this->subQuestion                 = $this->subSurveyQuestion->question;
            $this->orderSubQuestion            = $this->subSurveyQuestion->position;
            $this->requiredSubQuestion         = $this->subSurveyQuestion->mandatory;
            $this->subTypeSelected             = $this->subQuestion->type;
            $this->selectedParentQuestionId    = $this->subSurveyQuestion->id;
            $this->selectedParentQuestion      = $this->subSurveyQuestion->parent->question;
            $this->parentQuestionRadio         = $this->subSurveyQuestion->condition;
            $this->subQuestionName['es']       = $this->subQuestion->getTranslation('content', 'es');
            $this->subQuestionName['en']       = $this->subQuestion->getTranslation('content', 'en');
            $this->subEditModeQuestion         = true;
            if ($this->subTypeSelected == "multiselect" || $this->subTypeSelected == "uniqueselect") {
                $this->customSubOptions = true;
                $this->subOptionEs = $this->question->getTranslation('options', 'es');
                $this->subOptionEn = $this->question->getTranslation('options', 'en');
            }
        }
    }

    public function deleteQuestion($id)
    {
        SurveyQuestion::destroy($id);
        $this->survey->refresh();
    }

    public function resetValues()
    {
        $this->question             = new Question();
        $this->subQuestion          = new Question();
        $this->editModeQuestion     = false;
        $this->subEditModeQuestion  = false;
        $this->orderQuestion        = null;
        $this->requiredQuestion     = false;
        $this->optionEN             = [];
        $this->optionES             = [];
        $this->customOptions        = false;

        $this->reset(['typeSelected', 'subTypeSelected', 'questionName', 'subQuestionName', 'selectedParentQuestionId', 'selectedParentQuestion', 'selectedDefaultQuestion', 'selectedDefaultQuestionOrder', 'parentQuestionRadio']);
    }

    public function closeSurvey()
    {
        $surveyService  = new SurveyService();
        $result         = $surveyService->closeSurvey($this->survey, Constants::SURVEY_STATUS_CLOSED);

        if ($result) {
            session()->flash('surveySended',  'Formulario cerrado');
            return redirect(route('survey.list'));
        } else {
            session()->flash('surveySended',  'Error al cerrar el formulario');
        }
    }

    public function updatedTypeSelected()
    {
        switch ($this->typeSelected) {
            case "radio":
                $this->optionES = [
                    'SI',
                    'NO',
                    'NP'
                ];
                $this->optionEN = [
                    'YES',
                    'NO',
                    'NA'
                ];
                $this->customOptions = false;
                break;
            case "multiselect":
                $this->optionES = [];
                $this->optionEN = [];
                $this->customOptions = true;
                break;
            case "uniqueselect":
                $this->optionES = [];
                $this->optionEN = [];
                $this->customOptions = true;
                break;
            default:
                $this->optionES = [];
                $this->optionEN = [];
                $this->customOptions = false;
                break;
        }
    }

    public function updatedSurveyExpiration()
    {
        if ($this->survey->status ==  Constants::SURVEY_STATUS_PROCESS) {
            session()->flash('survey-expiration-update', '');
        }
    }

    public function updatedSelectedParentQuestionId()
    {
        $surveyQuestion = SurveyQuestion::find($this->selectedParentQuestionId);
        $this->selectedParentQuestion = $surveyQuestion->question;
        if ($this->selectedParentQuestion->type == "multiselect" || $this->selectedParentQuestion->type == "uniqueselect") {
            $this->parentQuestionRadio = "000";
        }
        $this->defaultQuestionsSub = (new SurveyService())->getQuestions($this->survey, Section::find($surveyQuestion->section_id));
    }

    public function updatedSelectedProfessionalId()
    {
        $this->selectedProfessional = config('iworking.user-model')::find($this->selectedProfessionalId);
        if ($this->selectedProfessional) {
            $this->professionalSelectOptions["jobTitles"] = config('iworking.job-titles')::select('*')->where('user_type_id', $this->selectedProfessional->type)->orderBy('name', 'asc')->get();
        } else {
            $this->professionalSelectOptions["jobTitles"] = [];
        }
    }

    public function updatedSectionQuestionSelected()
    {
        $this->defaultQuestions = (new SurveyService())->getQuestions($this->survey, Section::find($this->sectionQuestionSelected));
        $this->question = new Question();
        $this->selectedDefaultQuestion = null;
        $this->questionName = [
            'es'    => '',
            'en'    => ''
        ];
        $this->typeSelected = null;
    }

    public function updateExpirationSurvey()
    {
        if ($this->survey->status ==  Constants::SURVEY_STATUS_PROCESS) {
            $this->survey->save();
            $totalRemindersSent = 0;
            if ($this->survey->id) {
                $totalRemindersSent = $this->sendReminder();
            }
            session()->flash('survey-expiration-updated', 'Se ha actualizado correctamente la fecha de expiración del formulario. <br> Se enviaron ' . $totalRemindersSent . ' recordatorios por mail.');
        }
    }

    /**
     * Send reminder emails
     * Return number of emails sent
     * @return int
     */
    public function sendReminder(): int
    {
        $reminders =  Entry::where('survey_id', $this->survey->id)
            ->where('status', Constants::ENTRY_STATUS_PENDING)
            ->get();

        $totalRemindersSent = 0;
        foreach ($reminders as $reminder) {
            try {
                Mail::mailer('custom')->to($reminder->participant)->send(new ReminderNotification($reminder));
                $totalRemindersSent++;
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
        return $totalRemindersSent;
    }

    public function isActive($id)
    {
        $surVeyQuestion = SurveyQuestion::find($id);
        return !$surVeyQuestion->disabled ?? false;
    }

    public function activeQuestion($id)
    {
        $surVeyQuestion = SurveyQuestion::find($id);
        $surVeyQuestion->update(['disabled' => (!$surVeyQuestion->disabled)]);
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

            $data = ['survey' => $this->survey, 'onlyOrder' => false];
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, "isPhpEnabled" => true])->loadView('survey::exports.pdf-survey', $data);
            $pdf = $pdf->output();
            return response()->streamDownload(
                fn () => print($pdf),
                $name . '.pdf'
            );
        } catch (Exception $e) {
            dd($data, $e->getMessage());
        }
    }

    public function exportOrderToPDF()
    {
        try {
            $name = "Comanda_" . $this->survey->survey_number . "_";;
            if ($this->survey->type == "pharmaciesSale") {
                $name .= "Venta_Farmacia";
            } elseif ($this->survey->type == "medicalPrescription") {
                $name .= "Prescripción_Médica";
            } elseif ($this->survey->type == "general") {
                $name .= "General";
            } else {
                $name .= "Formación";
            }
            $data = ['survey' => $this->survey, 'onlyOrder' => true];
            $pdf = PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true, "isPhpEnabled" => true])->loadView('survey::exports.pdf-survey', $data);
            $pdf = $pdf->output();
            return response()->streamDownload(
                fn () => print($pdf),
                $name . '.pdf'
            );
        } catch (Exception $e) {
            dd($data, $e->getMessage());
        }
    }

    public function exportOrderToExcel()
    {
        $name = "Comanda_" . $this->survey->survey_number . "_";;
        if ($this->survey->type == "pharmaciesSale") {
            $name .= "Venta_Farmacia";
        } elseif ($this->survey->type == "medicalPrescription") {
            $name .= "Prescripción_Médica";
        } elseif ($this->survey->type == "general") {
            $name .= "General";
        } else {
            $name .= "Formación";
        }
        $path = tempnam(sys_get_temp_dir(), "FOO");
        $this->exportToExcel($path);

        return response()->download($path, $name . '.xlsx', [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . $name . '.xlsx"'
        ]);
    }


    public function exportToExcel($path)
    {

        $list = collect([
            [
                'Tipo de pedido' => '',
                'Producto' =>  '',
                'Unidades' =>  '',
                'Facturación sin iva' =>  '',
                'Motivos no interesado' =>   '',
                'Comentarios' =>   ''

            ]
        ]);

        return (new FastExcel($list))->export($path, function ($entry) {
            return [
                'Tipo de pedido' => '',
                'Producto' =>  '',
                'Unidades' =>  '',
                'Facturación sin iva' =>  '',
                'Motivos no interesado' =>   '',
                'Comentarios' =>   ''

            ];
        });
    }
}
