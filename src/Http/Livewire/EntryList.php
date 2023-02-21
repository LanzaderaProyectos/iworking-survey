<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Mail\ReminderNotification;

class EntryList extends Component
{
    public $entries = 10;
    public $surveyId;
    public $totalPoints = 0;
    public $filtersMode = false;
    public $search = "";

    public function mount()
    {
        $this->search = [
            'surveyed'      => '',
            'manager'       => '',
            'status'        => '',
            'min'           => '',
            'max'           => ''
        ];
        $this->surveyId = Route::current()->parameter('surveyId');
        $this->totalPoints = Question::where('type', 'radio')
            ->where('survey_id', $this->surveyId)
            ->count() * 100;
    }

    public function render()
    {
        $entries = Entry::where('survey_id', $this->surveyId)
        ->tableSearch($this->search)
        ->with(['surveyed']);
        
        return view('survey::livewire.entry-list', [
            'surveyEntries' => $entries->get()
        ]);
    }

    public function sendReminder()
    {
        $reminders =  Entry::where('survey_id', $this->surveyId)
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
        session()->flash('reminderMails', 'Se enviaron ' . $totalRemindersSent . ' recordatorios por mail.');
    }

    public function exportToExcel($path)
    {
        $entries = Entry::where('survey_id', $this->surveyId);
        $entries->tableSearch($this->search)
            ->with(['surveyed']);

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
        $entries->tableSearch($this->search)
            ->with(['surveyed']);

        $data = [
            'surveyEntries' => $entries->get(),
            'totalPoints'   => $this->totalPoints
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
