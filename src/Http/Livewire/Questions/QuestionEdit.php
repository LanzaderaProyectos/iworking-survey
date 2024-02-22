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
        'text' => 'Texto',
        'radio' => 'Opción',
        // 'number' => 'Numero'
    ];
    public $optionES = [];
    public $optionEN = [];
    public $typeSelected;


    protected $rules = [
        'question.comments' => 'nullable',
        'questionName.es'   => 'required',
        'questionName.en'   => 'nullable',
        'typeSelected'      => 'required',
    ];


    public function mount($questionId = null)
    {
        if ($questionId) {
            $this->question = Question::find($questionId);
            if ($this->question->survey_id != null || $this->question->section_id != null)  {
                abort(403, 'This question is already assigned to a survey or section');
            }else{
                $this->questionName = [
                    'es' => $this->question->getTranslation('content', 'es'),
                    'en' => $this->question->getTranslation('content', 'en')
                ];
                $this->typeSelected = $this->question->type;
            }
        } else {
            $this->question = new Question();
        }

    }

    public function render()
    {
        return view('survey::livewire.questions.question-edit', [
        ]);
    }

    public function save()
    {
        $this->validate();

        $questionService    = new QuestionService();
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

    public function cancel()
    {
        return redirect()->route('questions.list');
    }
}
