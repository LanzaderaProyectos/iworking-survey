<?php

namespace MattDaneshvar\Survey\Http\Livewire\Questions;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade as PDF;
use MattDaneshvar\Survey\Models\Question;
use Rap2hpoutre\FastExcel\FastExcel;
use MattDaneshvar\Survey\Models\Survey;

class QuestionList extends Component
{
    // ToDo - Search - Fix manager relationship. Total_amount is always null and totalLines() or appended calculated_total are not searchable
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $entries             = 10;
    public $sortDirection       = 'asc';
    public $sortBy              = 'code';
    public $orderLinePA     = null;
    public $search          = '';

    public $filters = [
        "code" => "",
        "name" => "",
        "type" => "",
        "form_type" => "",
        "created_from" => "",
        "created_to" => "",
        "disabled" => false,
        "disabled_from" => "",
        "disabled_to" => ""
    ];
    public $filtersMode = false;

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


    public $questionTypes = [
        "general" => "General",
        "pharmaciesSale" => "Venta Farmacias",
        "medicalPrescription" => "Prescripción Médica",
        "training" => "Formación",
    ];


    public function mount()
    {
    }

    public function sortByTable($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    public function updatingEntries()
    {
        $this->resetPage();
    }

    public function updatedFilters()
    {
        if ($this->filters != [
            "code" => "",
            "name" => "",
            "type" => "",
            "form_type" => "",
            "created_from" => "",
            "created_to" => "",
            "disabled" => false,
            "disabled_from" => "",
            "disabled_to" => ""
        ]) {
            $this->filtersMode = true;
        } else {
            $this->filtersMode = false;
        }
    }

    /**
     * Deletes all the previous saved filters on the user session
     * and reset the filters array $search
     * @var array $search
     *
     * @return void
     */
    public function clearFilters()
    {
        $this->filters = [
            "code" => "",
            "name" => "",
            "type" => "",
            "form_type" => "",
            "created_from" => "",
            "created_to" => "",
            "disabled" => false,
            "disabled_from" => "",
            "disabled_to" => ""
        ];
        $this->filtersMode = false;
    }

    public function render()
    {
        $questions = Question::select('*');

        $questions->filters($this->filters);

        return view('survey::livewire.questions.question-list', [
            'questions' => $questions->orderBy($this->sortBy, $this->sortDirection)->paginate($this->entries)
        ]);
    }

    public function exportToExcel($path)
    {
        $questions = Question::orderBy($this->sortBy, $this->sortDirection);
        $questions->filters($this->filters);

        return (new FastExcel($questions->get()))->export($path, function ($question) {
            if ($question->disabled) {
                $status = "Desactivada";
                $disabledAt = auth()->user()->applyDateFormat($question->disabled_at);
            } else {
                $status = "Activa";
                $disabledAt = "-";
            }
            return [
                'Codigo' => $question->code ?? '',
                'Nombre' => $question->getTranslation('content', 'es') ?? '',
                'Tipo' => $this->typeAnwers[$question->type] ?? '',
                'Tipo Formulario' =>  $this->questionTypes[$question->survey_type] ?? '',
                'Fecha creación' =>  auth()->user()->applyDateFormat($question->created_at) ?? '',
                'Estado' =>  $status,
                'Fecha desactivación' =>  $disabledAt,
            ];
        });
    }



    /**
     * Downloads excel file generated in exportToExcel()
     *
     * @return mixed
     */
    public function downloadExcel()
    {
        $path = tempnam(sys_get_temp_dir(), "FOO");
        $this->exportToExcel($path);

        return response()->download($path, 'Listado de preguntas' . '.xlsx', [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . 'Listado de preguntas' . '.xlsx"'
        ]);
    }

    /**
     * Generates and download a PDF file
     *
     * @return mixed
     */
    public function exportToPDF()
    {
        $questions = Question::whereNull('survey_id')->whereNull('section_id')->orderBy($this->sortBy, $this->sortDirection);
        if ($this->search != '') {
            $questions->where('content', 'like', '%' . $this->search . '%');
        }
        $data = [
            'preguntas' => $questions->get()
        ];
        $pdf = PDF::loadView('survey::exports.pdf-questions', $data)
            ->setPaper('a4', 'landscape')
            ->output();
        return response()->streamDownload(
            fn () => print($pdf),
            'Listado de preguntas.pdf'
        );
    }
}
