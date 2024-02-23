<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Section;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Models\Surveyed;
use MattDaneshvar\Survey\Library\Constants;
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
    public $users           = [];
    public $typeSelected    = null;
    public $subTypeSelected = null;
    public $newSurvey       = true;
    public $draft;
    public $typeAnwers = [
        'text' => 'Texto',
        'radio' => 'Opción',
        // 'number' => 'Numero'
    ];
    public $subTypeAnwers = [
        'text' => 'Texto',
        'radio' => 'Opción',
        // 'number' => 'Numero'
    ];
    public $editModeQuestion    = false;
    public $subEditModeQuestion = false;
    public $formEdit;
    public $optionES = [];
    public $optionEN = [];
    public $defaultQuestions;

    public $selectedParentQuestionId;
    public $selectedParentQuestion;
    public $selectedDefaultQuestion;
    public $selectedDefaultQuestionOrder;
    public $parentQuestionRadio;

    protected $rules = [
        //Survey
        'survey.expiration'     => 'required',
        'survey.comments'       => 'nullable',
        'surveyName.es'         => 'required',
        'surveyName.en'         => 'required',
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
    ];

    public function mount($draft = false)
    {
        $this->draft            = $draft;
        $this->formEdit         = !Route::is('survey.show');
        $this->defaultQuestions = (new QuestionService())->getDefaultQuestions();
        $this->initComponent();
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

    public function saveSurvey()
    {
        $this->validate();
        $surveyService          = new SurveyService();
        $result                 = $surveyService->saveSurvey(
            survey: $this->survey,
            authorId: auth()->user()->id,
            surveyName: $this->surveyName,
            surveyStatus: Constants::SURVEY_STATUS_DRAFT,
            auditText: 'Encuesta creada',
            newSurvey: $this->newSurvey
        );

        if ($result) {

            if ($this->newSurvey) {
                session()->flash('draftSurveyCreated', 'Encuesta creada');
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
        $this->survey->questions()->delete();
        $this->survey->sections()->delete();
        $this->survey->delete();
        session()->flash('surveyDeleted', 'Encuesta eliminada');

        return redirect(route('survey.list'));
    }

    public function sendSurvey()
    {
        $this->users = Surveyed::where('survey_id', $this->survey->id)->get();
        if (!$this->users->count()) {
            session()->flash('userListEmpty', 'No hay usuarios');
            return;
        }

        $surveyService  = new SurveyService();
        $result         = $surveyService->sendSurvey(
            users: $this->users,
            survey: $this->survey,
            audit: true,
            auditText: 'Encuesta enviada'
        );

        if ($result) {
            session()->flash('surveySended',  'Encuesta enviada');
            return redirect(route('survey.list'));
        } else {
            session()->flash('surveySended',  'Error al enviar la encuesta');
        }
    }

    public function addSection()
    {
        $this->validate([
            'sectionName.es' => 'required',
            'sectionName.en' => 'required',
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
            'questionName.es'       => 'required',
            'questionName.en'       => 'required',
            'question.section_id'   => 'required',
            'question.order'        => 'nullable|numeric',
        ]);

        $questionService    = new QuestionService();
        $result             = $questionService->saveQuestion(
            question: $this->question,
            surveyId: $this->survey->id,
            questionType: $this->typeSelected,
            questionName: $this->questionName,
            optionES: $this->optionES,
            optionEN: $this->optionEN
        );


        if ($result) {
            $this->question->update(['original_id' => $this->question->id]);
            $this->reset('questionName');
            $this->resetValues();
            session()->flash('questionSaved', 'Pregunta guardada');
            $this->survey->refresh();
        } else {
            session()->flash('questionSaved', 'Error al guardar la pregunta');
        }
    }

    public function saveSubQuestion()
    {

        $this->validate([
            'subQuestionName.es'       => 'required',
            'subQuestionName.en'       => 'required',
            'subQuestion.order'        => 'nullable|numeric',
        ]);

        // IDs
        if (!$this->subQuestion->id) {
            $parentId                       = $this->selectedParentQuestion->id;
            $this->subQuestion->parent_id   = $parentId;
        }

        $originalId                     = $this->selectedParentQuestion->original_id;
        $sectionId                      = $this->selectedParentQuestion->section_id;

        // SubQuestion Attributes
        $this->subQuestion->survey_id   = $this->survey->id;
        $this->subQuestion->section_id  = $sectionId;
        $this->subQuestion->original_id = $originalId;
        $this->subQuestion->condition   = $this->parentQuestionRadio;

        $questionService    = new QuestionService();
        $result             = $questionService->saveQuestion(
            question: $this->subQuestion,
            surveyId: $this->survey->id,
            questionType: $this->subTypeSelected,
            questionName: $this->subQuestionName,
            optionES: $this->optionES,
            optionEN: $this->optionEN
        );

        if ($result) {
            $this->reset('parentQuestionRadio');
            $this->reset('selectedParentQuestionId');
            $this->resetValues();
            session()->flash('subQuestionSaved', 'Sub pregunta guardada');
            $this->survey->refresh();
        } else {
            session()->flash('subQuestionSaved', 'Error al guardar la sub pregunta');
        }

        $this->subQuestion = new Question();
    }

    public function addDefaultQuestion($isOriginal = true)
    {
        try {
            $this->validate([
                'selectedDefaultQuestion'       => 'required',
                'selectedDefaultQuestionOrder'  => 'nullable|numeric',
            ], [
                'selectedDefaultQuestion.required' => 'Seleccione una pregunta por defecto',
            ]);
    
            $copyQuestion                   = Question::find($this->selectedDefaultQuestion)->toArray();
            $copyQuestion['survey_id']      = $this->survey->id;
    
            if ($isOriginal) {
                $this->validate([
                    'question.section_id' => 'required',
                ], [
                    'question.section_id.required' => 'Seleccione una sección',
                ]);

                $sectionId  = $this->question->section_id;
                $originalId = null;
                $parentId   = null;
            } elseif (!$isOriginal) {
                $this->validate([
                    'selectedParentQuestion.section_id'     => 'required',
                    'selectedParentQuestion.original_id'    => 'required',
                    'selectedParentQuestionId'              => 'required',
                ], [
                    'selectedParentQuestion.section_id.required' => 'Seleccione una sección',
                    'selectedParentQuestion.original_id.required' => 'Seleccione una pregunta padre',
                    'selectedParentQuestionId.required' => 'Seleccione una pregunta padre',
                
                ]);
                $sectionId  = $this->selectedParentQuestion->section_id;
                $originalId = $this->selectedParentQuestion->original_id;
                $parentId   = $this->selectedParentQuestionId;
            }
    
            $questionService                = new QuestionService();
            $question                       = $questionService->copyQuestion(
                isOriginal: $isOriginal,
                selectedDefaultQuestion: $this->selectedDefaultQuestion,
                surveyId: $this->survey->id,
                selectedParentQuestionId: $this->selectedParentQuestionId,
                parentQuestionRadio: $this->parentQuestionRadio,
                originalId: $originalId,
                sectionId: $sectionId,
                parentId: $parentId,
                optionES: $this->optionES,
                optionEN: $this->optionEN
            );
    
            $this->survey->refresh();
            $this->resetValues();
        } catch (\Throwable $th) {
            session()->flash('alert', $th->getMessage());
        }
    }

    public function editQuestion($id, $subQuestion = false)
    {

        if (!$subQuestion) {
            $this->question                 = Question::find($id);
            $this->typeSelected             = $this->question->type;
            $this->questionName['es']       = $this->question->getTranslation('content', 'es');
            $this->questionName['en']       = $this->question->getTranslation('content', 'en');
            $this->editModeQuestion         = true;
        } else {
            $this->subQuestion                 = Question::find($id);
            $this->subTypeSelected             = $this->subQuestion->type;
            $this->subQuestionName['es']       = $this->subQuestion->getTranslation('content', 'es');
            $this->subQuestionName['en']       = $this->subQuestion->getTranslation('content', 'en');
            $this->subEditModeQuestion         = true;
            $this->selectedParentQuestionId    = $this->subQuestion->parent_id;
            $this->selectedParentQuestion      = $this->subQuestion;
            $this->parentQuestionRadio         = $this->subQuestion->condition;
        }
    }

    public function deleteQuestion($id)
    {
        Question::destroy($id);
        $this->survey->refresh();
    }

    public function resetValues()
    {
        $this->question             = new Question();
        $this->subQuestion          = new Question();
        $this->editModeQuestion     = false;
        $this->subEditModeQuestion  = false;

        $this->reset(['typeSelected', 'subTypeSelected', 'questionName', 'subQuestionName', 'selectedParentQuestionId', 'selectedParentQuestion', 'selectedDefaultQuestion', 'selectedDefaultQuestionOrder', 'parentQuestionRadio']);
    }

    public function closeSurvey()
    {
        $surveyService  = new SurveyService();
        $result         = $surveyService->closeSurvey($this->survey, Constants::SURVEY_STATUS_CLOSED);

        if ($result) {
            session()->flash('surveySended',  'Encuesta cerrada');
            return redirect(route('survey.list'));
        } else {
            session()->flash('surveySended',  'Error al cerrar la encuesta');
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
        $this->selectedParentQuestion = Question::find($this->selectedParentQuestionId);
    }

    public function updateExpirationSurvey()
    {
        if ($this->survey->status ==  Constants::SURVEY_STATUS_PROCESS) {
            $this->survey->save();
            $totalRemindersSent = 0;
            if ($this->survey->id) {
                $totalRemindersSent = $this->sendReminder();
            }
            session()->flash('survey-expiration-updated', 'Se ha actualizado correctamente la fecha de expiración de la encuesta. <br> Se enviaron ' . $totalRemindersSent . ' recordatorios por mail.');
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
}
