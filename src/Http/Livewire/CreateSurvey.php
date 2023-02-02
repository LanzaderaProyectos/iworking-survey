<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Section;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Models\Surveyed;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Mail\UserNotification;
use MattDaneshvar\Survey\Mail\ReminderNotification;

class CreateSurvey extends Component
{
    public $survey = null;
    public $surveyName = [
        'es'    => '',
        'en'    => ''
    ];
    public $section = null;
    public $sectionName = [
        'es'    => '',
        'en'    => ''
    ];
    public $question = null;
    public $questionName = [
        'es'    => '',
        'en'    => ''
    ];
    public $users = [];
    public $typeSelected = null;
    public $newSurvey = true;
    public $draft;
    public $typeAnwers = [
        'text' => 'Texto',
        'radio' => 'Opción',
        // 'number' => 'Numero'
    ];
    public $editModeQuestion = false;

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
    ];

    public function mount($draft = false)
    {
        $this->draft = $draft;
        $this->formEdit  = !Route::is('survey.show');
        $surveyId = Route::current()->parameter('surveyId');
        if ($surveyId) {
            $this->survey = Survey::find($surveyId);
            $this->surveyName['es'] = $this->survey->getTranslation('name', 'es');
            $this->surveyName['en'] = $this->survey->getTranslation('name', 'en');
            $this->newSurvey = false;
            $this->question = new Question();
            $this->section = new Section();
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
        $this->survey->author = auth()->user()->id;
        if ($this->newSurvey) {
            $lastSurveyNumber  = Survey::where('survey_number', '!=', null)->orderBy('survey_number', 'desc')->first();
            $this->survey->survey_number    = ($lastSurveyNumber && $lastSurveyNumber->survey_number > 0) ? $lastSurveyNumber->survey_number + 1 : 10000;
        }
        $this->survey
            ->setTranslation('name', 'es', $this->surveyName['es'])
            ->setTranslation('name', 'en', $this->surveyName['en']);
        $this->survey->save();

        if ($this->newSurvey) {
            $this->survey->status = Constants::SURVEY_STATUS_DRAFT;
            $this->survey->audit()->create([
                'user_id'   => auth()->id(),
                'status'    => Constants::SURVEY_STATUS_DRAFT,
                'text'      => 'Encuesta creada'
            ]);
            session()->flash('draftSurveyCreated', 'Encuesta creada');
            return redirect(route('survey.edit', [
                'surveyId' => $this->survey->id
            ]));
        }
        session()->flash('surveyUpdated', 'Cambios guardados');
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

        foreach ($this->users as $user) {
            try {
                Mail::mailer('custom')->to($user->email)->send(new UserNotification($this->survey, $user));
                Entry::create([
                    'survey_id' => $this->survey->id,
                    'participant' => $user->email,
                    'lang' => $user->lang,
                    'status' => Constants::ENTRY_STATUS_PENDING
                ]);
            } catch (\Exception $e) {
                $this->survey->audit()->create([
                    'user_id'   => auth()->id(),
                    'status'    => Constants::SURVEY_STATUS_SEND_ERROR,
                    'text'      => $user->email
                ]);
                Log::error($e);
            }
        }
        $this->survey->status = 1;
        $this->survey->save();
        $this->survey->audit()->create([
            'user_id'   => auth()->id(),
            'status'    => Constants::SURVEY_STATUS_PROCESS,
            'text'      => 'Encuesta enviada'
        ]);
        session()->flash('surveySended',  'Encuesta enviada');
        return redirect(route('survey.list'));
    }

    public function addSection()
    {
        $this->validate([
            'sectionName.es' => 'required',
            'sectionName.en' => 'required',
            'section.order' =>  'nullable|numeric'
        ]);
        $this->section->survey_id = $this->survey->id;;
        $this->section
            ->setTranslation('name', 'es', $this->sectionName['es'])
            ->setTranslation('name', 'en', $this->sectionName['en']);
        $this->section->save();
        $this->reset('sectionName');
        $this->section = new Section();
        $this->survey->refresh();
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
        if ($this->typeSelected == 'radio') {
            $this->question->setTranslation('options', 'es', $this->optionES)
                ->setTranslation('options', 'en', $this->optionEN);;
            $this->question->type = 'radio';
        } else {
            $this->question->type = 'text';
        }
        $this->question->survey_id = $this->survey->id;
        $this->question
            ->setTranslation('content', 'es', $this->questionName['es'])
            ->setTranslation('content', 'en', $this->questionName['en']);
        $this->question->save();
        $this->reset('questionName');
        $this->resetValues();
        session()->flash('questionSaved', 'Pregunta guardada');
        $this->survey->refresh();
    }

    public function editQuestion($id)
    {
        $this->question = Question::find($id);
        $this->typeSelected = $this->question->type;
        $this->questionName['es'] = $this->question->getTranslation('content', 'es');
        $this->questionName['en'] = $this->question->getTranslation('content', 'en');
        $this->editModeQuestion = true;
    }
    public function deleteQuestion($id)
    {
        Question::destroy($id);
        $this->survey->refresh();
    }

    public function resetValues()
    {
        $this->question = new Question();
        $this->reset('questionName');
        $this->reset(['typeSelected']);
        $this->editModeQuestion = false;
    }

    public function closeSurvey()
    {
        $this->survey->audit()->create([
            'user_id'   => auth()->id(),
            'status'    => Constants::SURVEY_STATUS_CLOSED,
            'text'      => 'Encuesta cerrada'
        ]);
        $this->survey->status = Constants::SURVEY_STATUS_CLOSED;
        $this->survey->save();
        session()->flash('surveySended',  'Encuesta cerrada');
        return redirect(route('survey.list'));
    }

    public function updatedSurveyExpiration()
    {
        if ($this->survey->status ==  Constants::SURVEY_STATUS_PROCESS) {
            $this->survey->save();
            $totalRemindersSent = 0;
            if ($this->survey->id) {
                $totalRemindersSent = $this->sendReminder();
            }
            session()->flash('survey-expiration-update', 'Se ha actualizado correctamente la fecha de expiración de la encuesta. <br> Se enviaron ' . $totalRemindersSent . ' recordatorios por mail.');
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
