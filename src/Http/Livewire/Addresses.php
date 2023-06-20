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
    public $allSurveyeds = [];
    public $unregisteredSurveyed = [];
    public $survey;
    public $surveyedsFromExcel;
    public $file;
    public $shippingMail;
    public $errorMessage;
    public $successMessage;

    protected $listeners = ['updatedSurveyed'];


    public function mount(Survey $survey)
    {
        $this->survey = $survey;
        $this->file = File::where('fileable_id', $this->survey->id)
            ->where('fileable_type', 'App\Surveyed')
            ->where('type', 'surveyed-excel')
            ->first();
        $this->surveyeds = Surveyed::where('survey_id', $this->survey->id)->get();
        $this->allSurveyeds = Surveyed::all()->unique('email');
        $this->unregisteredSurveyed['language'] = 'es';
    }

    public function render()
    {
        return view('survey::livewire.addresses');
    }

    public function updated($updatedKey, $updatedValue){
        if($updatedKey === "shippingMail"){
            $newSurveyed = Surveyed::where(['email' => $this->shippingMail])->get();
            $this->surveyeds = $this->surveyeds->concat($newSurveyed)->unique('email'); 
        }
    }

    public function createSurveyeds()
    {
        if(isset($this->surveyedsFromExcel)) {
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
        }
        if(isset($this->surveyeds)){
            foreach ($this->surveyeds as $item) {
                if($item->survey_id != $this->survey->id){
                    Surveyed::updateOrCreate(
                        [
                            'survey_id' => $this->survey->id,
                            'name' => $item->name,
                            'vat_number' => $item->vat_number,
                            'email' => $item->email,
                            'contact_person' => $item->contact_person,
                            'lang' => $item->lang,
                            'manager' => $item->manager
                        ]
                    );
                }
            }
        }
        $this->surveyeds = Surveyed::where('survey_id', $this->survey->id)->get();
        $this->successMessage = 'Los usuarios se cargaron con éxito.';
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

    public function addNewSurveyed()
    {
        $existingServeyed = Surveyed::where('email', $this->unregisteredSurveyed['email'])->get();
        
        if(!$existingServeyed->isEmpty()) {
            $this->errorMessage = 'Este email corresponde a un encuestado conocido.';
            return;
        }
        $newSurveyed = Surveyed::create([
            'survey_id'         => $this->survey->id,
            'name'              => $this->unregisteredSurveyed['name'],
            'vat_number'        => $this->unregisteredSurveyed['nif'],
            'contact_person'    => $this->unregisteredSurveyed['contactPerson'],
            'email'             => $this->unregisteredSurveyed['email'],
            'lang'              => $this->unregisteredSurveyed['language'],
            'manager'           => $this->unregisteredSurveyed['manager'],
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
        $current_surveyed = Surveyed::where(['id' => $newSurveyed->id])->get();
        $this->surveyeds = $this->surveyeds->concat($current_surveyed)->unique('id');
    }

    public function removeSurveyed($user)
    {
        $this->surveyeds = $this->surveyeds->reject(function ($current) use ($user) {
            return $current->id === $user['id'];
        });        
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
