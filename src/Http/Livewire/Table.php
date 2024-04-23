<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade as PDF;
use Rap2hpoutre\FastExcel\FastExcel;
use Iworking\IworkingBoilerplate\Library\Constants;
use MattDaneshvar\Survey\Models\Survey;

class Table extends Component
{
    // ToDo - Search - Fix manager relationship. Total_amount is always null and totalLines() or appended calculated_total are not searchable
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $entries             = 10;
    public $sortDirection       = 'desc';
    public $sortBy              = 'survey_number';
    public $filtersMode         = false;
    public $draft               = false;
    public $columsSelected = [];
    public $orderLinePA = null;
    public $search = [];

    public $surveyTypes = [
        'pharmaciesSale' => "Venta Farmacias",
        'medicalPrescription' => "Prescripción Médica",
        'training' => "Formación",
    ];


    public function mount()
    {
        $this->search = [
            'survey_number' => '',
            'name'          => '',
            'author'        => '',
            'status'        => '',
        ];
        // Filters saved on session
        if (session()->has('surveysearch')) {
            foreach ($this->search as $key => $filter) {
                if (session()->has('surveysearch.' . $key) == $key) {
                    $this->search[$key] = session()->get('surveysearch.' . $key)[0];
                }
            }
        }
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

    /**
     * Runs after any update to the Livewire component's data
     * (Using wire:model, not directly inside PHP)
     *
     * @param string $propertyName
     * @param mixed $value
     * @return void
     */
    public function updated($propertyName, $value)
    {
        session()->forget('survey' . $propertyName);

        //Save the search filters on user session
        if ($value != '') {
            session()->push('survey' . $propertyName, $value);
        }

        if (empty(session('surveysearch'))) {
            session()->forget('surveysearch');
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
        session()->forget('surveysearch');
        $this->search = [
            'survey_number' => '',
            'name'          => '',
            'author'        => '',
            'status'        => '',
        ];
    }

    public function render()
    {
        $surveys = Survey::orderBy($this->sortBy, $this->sortDirection);
        if ($this->draft) {
            $surveys->where('status', '=', 0);
        } else {
            $surveys->where('status', '>', 0);
        }
        $surveys->tableSearch($this->search);
        return view('survey::livewire.table', [
            'surveys' => $surveys->get()
        ]);
    }

    public function exportToExcel($path)
    {
        $surveys = Survey::orderBy($this->sortBy, $this->sortDirection);
        if ($this->draft) {
            $surveys->where('status', '=', 0);
        } else {
            $surveys->where('status', '>', 0);
        }
        $surveys->tableSearch($this->search);

        return (new FastExcel($surveys->get()))->export($path, function ($survey) {
            return [
                'Nº Formulario' => $survey->survey_number ?? '',
                'Nombre' => $survey->name ?? '',
                'Autor' => $survey->author ?? '',
                'Estado' => __('survey::status.survey.' . $survey->status) ?? '',
                'Fecha creación' =>  auth()->user()->applyDateFormat($survey->created_at) ?? '',
                'Vencimiento' =>  auth()->user()->applyDateFormat($survey->expiration) ?? '',
                'Media' =>  number_format(
                    $survey->entries->sum('sum_score') / $survey->entries->count(),
                    2,
                    ',',
                    '.'
                ),
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

        return response()->download($path, 'Formularios' . '.xlsx', [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . 'Formularios' . '.xlsx"'
        ]);
    }

    /**
     * Generates and download a PDF file
     *
     * @return mixed
     */
    public function exportToPDF()
    {
        $surveys = Survey::orderBy($this->sortBy, $this->sortDirection);
        if ($this->draft) {
            $surveys->where('status', '=', 0);
        } else {
            $surveys->where('status', '>', 0);
        }
        $surveys->tableSearch($this->search);
        $data = [
            'surveys' => $surveys->get()
        ];
        $pdf = PDF::loadView('survey::exports.pdf-surveys', $data)
            ->setPaper('a4', 'landscape')
            ->output();
        return response()->streamDownload(
            fn () => print($pdf),
            'surveys.pdf'
        );
    }
}
