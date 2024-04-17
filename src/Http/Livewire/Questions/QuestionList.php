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
    public $sortDirection       = 'desc';
    public $sortBy              = 'created_at';
    public $orderLinePA     = null;
    public $search          = '';


    public function mount()
    {
    }

    public function sortBy($field)
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

    /**
     * Deletes all the previous saved filters on the user session
     * and reset the filters array $search
     * @var array $search
     *
     * @return void
     */
    public function clearFilters()
    {
        $this->search = '';
    }

    public function render()
    {
        $questions = Question::orderBy($this->sortBy, $this->sortDirection);

        if ($this->search != '') {
            $questions->where('content', 'like', '%' . $this->search . '%');
        }

        return view('survey::livewire.questions.question-list', [
            'questions' => $questions->paginate($this->entries)
        ]);
    }

    public function exportToExcel($path)
    {
        $questions = Question::orderBy($this->sortBy, $this->sortDirection);
        if ($this->search != '') {
            $questions->where('content', 'like', '%' . $this->search . '%');
        }

        return (new FastExcel($questions->get()))->export($path, function ($question) {
            return [
                'Nombre' => $question->name ?? '',
                'Tipo' => $question->type ?? '',
                'Fecha creación' =>  auth()->user()->applyDateFormat($question->created_at) ?? '',
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
