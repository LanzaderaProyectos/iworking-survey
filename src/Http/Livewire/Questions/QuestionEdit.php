<?php

namespace MattDaneshvar\Survey\Http\Livewire\Questions;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade as PDF;
use MattDaneshvar\Survey\Models\Question;
use Rap2hpoutre\FastExcel\FastExcel;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Services\QuestionService;

class QuestionEdit extends Component
{
    // ToDo - Search - Fix manager relationship. Total_amount is always null and totalLines() or appended calculated_total are not searchable
    public Question $question;
    public $questionName = [
        'es' => '',
        'en' => ''
    ];
    public $typeAnwers = [
        'radio'         => 'Si/No/NP',
        'multiselect'   => 'Selección múltiple',
        'uniqueselect'  => 'Selección única',
        'date'          => 'Fecha',
        'hour'          => 'Hora',
        'text'          => 'Texto',
        'longText'      => 'Texto largo',
        'number'        => 'Númerico',
        'currency'      => 'Moneda'
        // 'number' => 'Numero'
    ];
    public $optionES = [];
    public $optionEN = [];
    public $typeSelected;
    public $surveyType;
    public $newOptionES = "";
    public $newOptionEN = "";
    public $customOptions = false;
    public $updateOption = null;
    public $questionTypes = [
        "general" => "General",
        "pharmaciesSale" => "Venta Farmacias",
        "medicalPrescription" => "Prescripción Médica",
        "training" => "Formación",
    ];


    protected $rules = [
        'question.comments' => 'nullable',
        'question.disabled' => 'nullable',
        'questionName.es'   => 'required',
        'questionName.en'   => 'nullable',
        'typeSelected'      => 'required'
    ];


    public function mount($questionId = null)
    {
        if ($questionId) {
            $this->question = Question::find($questionId);
            if ($this->question->survey_id != null || $this->question->section_id != null) {
                abort(403, 'This question is already assigned to a survey or section');
            } else {
                $this->questionName = [
                    'es' => $this->question->getTranslation('content', 'es'),
                    'en' => $this->question->getTranslation('content', 'en')
                ];
                $this->typeSelected = $this->question->type;
                $this->surveyType = $this->question->survey_type;
                switch ($this->typeSelected) {
                    case "radio":
                        $this->optionES = $this->question->getTranslation('options', 'es') ?? [];
                        $this->optionEN = $this->question->getTranslation('options', 'en') ?? [];
                        if($this->optionES == "")
                        {
                            $this->optionES = [];
                        }
                        if($this->optionEN == "")
                        {
                            $this->optionEN = [];
                        }
                        $this->customOptions = false;
                        break;
                    case "multiselect":
                        $this->optionES = $this->question->getTranslation('options', 'es') ?? [];
                        $this->optionEN = $this->question->getTranslation('options', 'en') ?? [];
                        if($this->optionES == "")
                        {
                            $this->optionES = [];
                        }
                        if($this->optionEN == "")
                        {
                            $this->optionEN = [];
                        }
                        $this->customOptions = true;
                        break;
                    case "uniqueselect":
                        $this->optionES = $this->question->getTranslation('options', 'es') ?? [];
                        $this->optionEN = $this->question->getTranslation('options', 'en') ?? [];
                        if($this->optionES == "")
                        {
                            $this->optionES = [];
                        }
                        if($this->optionEN == "")
                        {
                            $this->optionEN = [];
                        }
                        $this->customOptions = true;
                        break;
                    default:
                        $this->customOptions = false;
                        break;
                }
            }
        } else {
            $this->question = new Question();
        }
    }

    public function render()
    {
        return view('survey::livewire.questions.question-edit', []);
    }

    public function save()
    {
        $this->validate();
        $questionService    = new QuestionService();
        $this->question->survey_type = $this->surveyType;
        $result             = $questionService->saveQuestion(
            question: $this->question,
            surveyId: null,
            questionType: $this->typeSelected,
            questionName: $this->questionName,
            optionES: $this->optionES,
            optionEN: $this->optionEN
        );
        $this->question->save();
        return redirect()->route('questions.list')->with('status', 'Pregunta guardada con éxito');
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

    public function cancel()
    {
        return redirect()->route('questions.list');
    }
}
