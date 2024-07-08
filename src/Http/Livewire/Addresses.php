<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Surveyed;
use Rap2hpoutre\FastExcel\Facades\FastExcel;
use Iworking\IworkingBoilerplate\Models\File;


class Addresses extends Component
{
    public $surveyeds = [];
    public $survey;
    public $surveyedsFromExcel;
    public $file;

    protected $listeners = ['updatedSurveyed'];


    public function mount(Survey $survey)
    {
        $this->survey = $survey;
        $this->file = File::where('fileable_id', $this->survey->id)
            ->where('fileable_type', 'App\Surveyed')
            ->where('type', 'surveyed-excel')
            ->first();
        if ($this->file) {
            $this->surveyeds = Surveyed::where('survey_id', $this->survey->id)->get();
        }
    }

    public function render()
    {
        return view('survey::livewire.addresses');
    }

    public function createSurveyeds()
    {
        foreach ($this->surveyedsFromExcel as $item) {
            Surveyed::updateOrCreate(
                [
                    'email' => $item['Email']
                ],
                [
                    'survey_id' => $this->survey->id,
                    'name' => $item['Nombre'],
                    'vat_number' => $item['NIF'],
                    'contact_person' => $item['Contacto'],
                    'lang' => $item['Idioma'],
                    'manager' => $item['Responsable']
                ]
            );
        }
        $this->surveyeds = Surveyed::where('survey_id', $this->survey->id)->get();
    }


    public function updatedSurveyed()
    {
        $this->file = File::where('fileable_id', $this->survey->id)
            ->where('fileable_type', 'App\Surveyed')
            ->where('type', 'surveyed-excel')
            ->first();
        if ($this->file) {
            $this->createArrayFromFile();
        } else {
            Surveyed::where('survey_id', $this->survey->id)
                ->forceDelete();
            $this->resetValues();
        }
    }

    public function createArrayFromFile()
    {
        $excel = Storage::disk('s3')->get($this->file->cmis_url);
        Storage::disk('tmp')->put('./file_excel.xls', $excel);
        $pathExcel = Storage::disk('tmp')->path('/file_excel.xls');
        $this->surveyedsFromExcel = FastExcel::sheet(1)
            ->import($pathExcel);
        $this->createSurveyeds();
    }

    public function resetValues()
    {
        $this->reset(['surveyedsFromExcel', 'surveyeds']);
    }
}
