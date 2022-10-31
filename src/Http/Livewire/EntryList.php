<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Entry;
use Barryvdh\DomPDF\Facade as PDF;


class EntryList extends Component
{
    public $entries = 10;
    public $surveyId;
    public $search = "";

    public function mount()
    {
        $this->surveyId = Route::current()->parameter('surveyId');
    }

    public function render()
    {
        $entries = Entry::where('survey_id', $this->surveyId);
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $entries->whereHas('surveyed', function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('vat_number', 'like', $search)
                    ->orWhere('contact_person', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('manager', 'like', $search);
            });
        }
        $entries->with(['surveyed']);
        return view('survey::livewire.entry-list', [
            'surveyEntries' => $entries->get()
        ]);
    }

    public function exportToExcel($path)
    {
        $entries = Entry::where('survey_id', $this->surveyId);
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $entries->whereHas('surveyed', function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('vat_number', 'like', $search)
                    ->orWhere('contact_person', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('manager', 'like', $search);
            });
        }

        return (new FastExcel($entries->get()))->export($path, function ($entry) {
            return [
                'Encuestado' => $entry->surveyed->name ?? '',
                'Id' => $entry->surveyed->vat_number ?? '',
                'Email' => $entry->participant ?? '',
                'Persona contacto' => $entry->surveyed->contact_person ?? '',
                'Idioma' =>  $entry->lang ?? '',
                'Responsable' =>  $entry->surveyed->manager ?? '',
                'Estado' =>  __('survey::status.entry.' . $entry->status ?? ''),
                'Puntuación' =>  $entry->answers->sum('score')

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

        return response()->download($path, 'Entradas' . '.xlsx', [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'inline; filename="' . 'Entradas' . '.xlsx"'
        ]);
    }

    /**
     * Generates and download a PDF file
     *
     * @return mixed
     */
    public function exportToPDF()
    {
        $entries = Entry::where('survey_id', $this->surveyId);
        if ($this->search) {
            $search = '%' . $this->search . '%';
            $entries->whereHas('surveyed', function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('vat_number', 'like', $search)
                    ->orWhere('contact_person', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('manager', 'like', $search);
            });
        }
        $entries->with(['surveyed']);
        $data = [
            'surveyEntries' => $entries->get()
        ];
        $pdf = PDF::loadView('survey::exports.pdf-entries', $data)
            ->setPaper('a4', 'landscape')
            ->output();
        return response()->streamDownload(
            fn () => print($pdf),
            'surveys.pdf'
        );
    }
}
