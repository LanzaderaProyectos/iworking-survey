<?php

namespace MattDaneshvar\Survey\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use MattDaneshvar\Survey\Models\Entry;
use MattDaneshvar\Survey\Models\Survey;
use MattDaneshvar\Survey\Models\Section;
use MattDaneshvar\Survey\Models\Question;
use MattDaneshvar\Survey\Library\Constants;
use MattDaneshvar\Survey\Mail\UserNotification;

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

    public function sendSurvey()
    {
        $prueba = [
            (object)array("codigo" => "102852", "nombre" => "Carlos Bernuy", "nif" => "N2760536I", "tipo" => "no stock", "contacto" => "Maria Voß", "email" => "cbernuy@lanzaderaproyectos.com", "idioma" => "en", "empleado" => "E0387", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "101256", "nombre" => "Fran Campos", "nif" => "W0065869J", "tipo" => "stock", "contacto" => "Nuria Moragues", "email" => "fcampos@strategying.com", "idioma" => "en", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
        ];
        foreach ($prueba as $user) {
            try {
                Entry::create([
                    'survey_id' => $this->survey->id,
                    'participant' => $user->email,
                    'lang' => $user->idioma,
                    'status' => Constants::ENTRY_STATUS_PENDING
                ]);
                Mail::to($user->email)->send(new UserNotification($this->survey, $user));
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


    public function resetValues()
    {
        $this->question = new Question();
        $this->reset('questionName');
        $this->reset(['typeSelected']);
        $this->editModeQuestion = false;
    }

    public function uploadUsers()
    {
        $this->users = array(
            (object)array("codigo" => "102852", "nombre" => "CHEPLAPHARM ARZNEIMITTEL GMBH", "nif" => "N2760536I", "tipo" => "no stock", "contacto" => "Maria Voß", "email" => "maria.voss@cheplapharm.com", "idioma" => "EN", "empleado" => "E0387", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "101256", "nombre" => "PUROLITE (INT. ) LTD", "nif" => "W0065869J", "tipo" => "stock", "contacto" => "Nuria Moragues", "email" => "nuria.moragues@purolite.com", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "104667", "nombre" => "BESINS HEALTHCARE DISTRIBUTION FZ-L", "nif" => "BE0738781989", "tipo" => "stock", "contacto" => "Philippe Cornu", "email" => "PCORNU@besins-healthcare.com", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "100200", "nombre" => "AZELIS ESPAÑA, S.A.", "nif" => "A61744033", "tipo" => "stock", "contacto" => "Lourdes Montserrat", "email" => "lourdes.montserrat@azelis.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101210", "nombre" => "IPCA LABORATORIES LIMITED", "nif" => "0000101210", "tipo" => "stock", "contacto" => "Apurva Gavankar", "email" => "apurva.gavankar@ipca.com", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101243", "nombre" => "FINORTEX, S.L", "nif" => "B59942656", "tipo" => "stock", "contacto" => "Xavier Cirera", "email" => "finortex@firnotex.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "103420", "nombre" => "ACCORD HEALTHCARE SLU", "nif" => "B65112930", "tipo" => "stock", "contacto" => "Alicia Mena", "email" => "amena@accord-healthcare.com", "idioma" => "ES", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "100017", "nombre" => "MYLAN TEORANTA", "nif" => "IE4869322G", "tipo" => "stock", "contacto" => "Carina Almeida", "email" => "carina.Almeida@viatris.com", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "101392", "nombre" => "INGENIERIA TECNICA DE MANTENIMIENTO", "nif" => "A08962300", "tipo" => "no stock", "contacto" => "Daniel Martin", "email" => "soporte@inteman.net", "idioma" => "ES", "empleado" => "E0527", "responsable" => "Víctor Barrantes"),
            (object)array("codigo" => "100769", "nombre" => "SERB SA", "nif" => "BE0538813719", "tipo" => "stock", "contacto" => "Elisabeth de Almeida", "email" => "e.dealmeida@serb.eu", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "102667", "nombre" => "XEOLAS PHARMACEUTICALS LIMITED", "nif" => "IE9662814S", "tipo" => "stock", "contacto" => "Dennis McDaid", "email" => "dennis.mcdaid@xeolas.com", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "103006", "nombre" => "PICKING FARMA, S.A.", "nif" => "A61769204", "tipo" => "no stock", "contacto" => "Sergi Rodriguez Lopera", "email" => "srlopera@pickingfarma.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "100216", "nombre" => "LABIANA PHARMACEUTICALS, S.L.U.", "nif" => "B63014856", "tipo" => "stock", "contacto" => "Gerard Muñoz Estoquera", "email" => "gerard.munozlabiana.com", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "104141", "nombre" => "ONE WAY LIVER SL", "nif" => "B83390443", "tipo" => "stock", "contacto" => "Rebeca Mayo", "email" => "rmayo@owlmetabolomics.com", "idioma" => "ES", "empleado" => "E0446", "responsable" => "Raquel Cascante"),
            (object)array("codigo" => "104110", "nombre" => "FERMION Oy", "nif" => "FI18552129", "tipo" => "stock", "contacto" => "Tea Paloheimo", "email" => "Tea.Paloheimo@fermion.fi", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "103788", "nombre" => "CLIANTHA RESEARCH LTD", "nif" => "103787", "tipo" => "no stock", "contacto" => "Priyam Saxena", "email" => "psaxena@cliantha.com", "idioma" => "EN", "empleado" => "E0450", "responsable" => "Miguel Angel García"),
            (object)array("codigo" => "101618", "nombre" => "LABORATOIRES SERB", "nif" => "FR16552005241", "tipo" => "stock", "contacto" => "Elisabeth de Almeida", "email" => "e.dealmeida@serb.eu", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "104228", "nombre" => "ESPECIALISTAS EN TRABAJO TEMPORAL", "nif" => "A48630719", "tipo" => "no stock", "contacto" => "David Valero", "email" => "dvalero@grupoalliance.com", "idioma" => "ES", "empleado" => "E0419", "responsable" => "Sara Larrubia"),
            (object)array("codigo" => "102898", "nombre" => "BALKANPHARMA DUPNITSA AD", "nif" => "BG819364374", "tipo" => "stock", "contacto" => "n/a", "email" => "dupoperations@actavis.bg", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "100287", "nombre" => "ROTOR PRINT, S.L.", "nif" => "B60672623", "tipo" => "stock", "contacto" => "Jordi Franco", "email" => "j.franco@rotorprint.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101231", "nombre" => "IQVIA INFORMATION, S.A.", "nif" => "A28117463", "tipo" => "no stock", "contacto" => "Vazquez Segura, Jose Luis", "email" => "JoseLuis.VAZQUEZSEGURA@iqvia.com", "idioma" => "ES", "empleado" => "E0508", "responsable" => "Alejandro Tamayo"),
            (object)array("codigo" => "101001", "nombre" => "MACULART  S.A", "nif" => "A08786014", "tipo" => "stock", "contacto" => "David Majós", "email" => "david@maculart.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "100858", "nombre" => "ABBOTT RAPID DIAGNOSTICS HEALTHCARE", "nif" => "B58882952", "tipo" => "stock", "contacto" => "Jose Miguel Garcia", "email" => "josemiguel.garcia@abbott.com", "idioma" => "ES", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "100224", "nombre" => "SAFIC-ALCÁN ESPECIALIDADES, S.A.U.", "nif" => "A64927908", "tipo" => "stock", "contacto" => "Esther Vilanova", "email" => "evilanova@safic-alcan.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "100428", "nombre" => "ARVAL SERVICE LEASE SA", "nif" => "A81573479", "tipo" => "no stock", "contacto" => "Equipo Moscou", "email" => "equipo.moscu@arval.es", "idioma" => "ES", "empleado" => "E0387", "responsable" => "Alba Marín"),
            (object)array("codigo" => "100027", "nombre" => "WATERS CROMATOGRAFIA, S.A", "nif" => "A60631835", "tipo" => "no stock", "contacto" => "Antonio Fuentes", "email" => "antonio_fuentes@waters.com", "idioma" => "ES", "empleado" => "E0077", "responsable" => "Sergio Sanllorente"),
            (object)array("codigo" => "101481", "nombre" => "SIMSAGROUP TECHNOLOGIES, S.A.", "nif" => "A08673576", "tipo" => "no stock", "contacto" => "Jorge Sáez", "email" => "jsaez@simsagroup.com", "idioma" => "ES", "empleado" => "E0328", "responsable" => "Silvia Casellas"),
            (object)array("codigo" => "102497", "nombre" => "EUROAPY HUNGARY (GLOBAL K)", "nif" => "HU17780948", "tipo" => "stock", "contacto" => "Maria gonzález", "email" => "maria.gonzalez@globalk.es", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "102173", "nombre" => "HEALTH PASS COMUNICATION, SL.", "nif" => "B87368833", "tipo" => "no stock", "contacto" => "Alejandro Santos", "email" => "a.santos@healthpass.es", "idioma" => "ES", "empleado" => "E0514", "responsable" => "Montserrat Aguilar"),
            (object)array("codigo" => "103854", "nombre" => "SYNTHON BV", "nif" => "N0033110H", "tipo" => "stock", "contacto" => "Esther de Bot", "email" => "esther.debot@synthon.com", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "101489", "nombre" => "ENDESA ENERGIA, S.A.", "nif" => "A81948077", "tipo" => "no stock", "contacto" => "Marcos Ferrer", "email" => "marcos.ferrer@endesa.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "104831", "nombre" => "PUROLITE LTD", "nif" => "FR24429041189", "tipo" => "stock", "contacto" => "Nuria Moragues", "email" => "nuria.moragues@purolite.com", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "105595", "nombre" => "CORPI OBRES I PROMOCIONS, SL", "nif" => "B17570177", "tipo" => "no stock", "contacto" => "Bartomeu Pinart Nadal", "email" => "bartomeu@corpi.cat", "idioma" => "ES", "empleado" => "E0328", "responsable" => "Silvia Casellas"),
            (object)array("codigo" => "100795", "nombre" => "CARTONAJES PANS, S.A", "nif" => "A08468738", "tipo" => "stock", "contacto" => "Víctor Constant", "email" => "vconstan@pans.net", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "104760", "nombre" => "RECIPHARM PARETS SLU", "nif" => "B65376055", "tipo" => "no stock", "contacto" => "Arantxa Golbano Rodríguez", "email" => "arantxa.golbano@recipharm.com", "idioma" => "ES", "empleado" => "E0450", "responsable" => "Eduardo Carretero"),
            (object)array("codigo" => "103336", "nombre" => "TRACELINK, INC", "nif" => "800451564", "tipo" => "no stock", "contacto" => "Itziar Escudero", "email" => "iescudero@tracelink.com", "idioma" => "EN", "empleado" => "E0508", "responsable" => "Alejandro Tamayo"),
            (object)array("codigo" => "100998", "nombre" => "OPERADORES LOGISTICOS DEL", "nif" => "A30347280", "tipo" => "no stock", "contacto" => "Francisco Lopez Navarro", "email" => "francisco.lopez@olmed.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "103489", "nombre" => "PEAK SPAIN SL", "nif" => "B87642815", "tipo" => "stock", "contacto" => "Jordi Bacardit", "email" => "jordi.bacardit@dupont.com", "idioma" => "ES", "empleado" => "E0027", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101368", "nombre" => "EMPRESAS RAMONEDA, S.A.", "nif" => "A08227829", "tipo" => "no stock", "contacto" => "Mª Jose Mateu", "email" => "mjosemateu@ramoneda.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "103670", "nombre" => "VALPHARMA INTERNATIONAL SPA", "nif" => "IT01351110414", "tipo" => "no stock", "contacto" => "Nicoletta Baldoni", "email" => "nicoletta.baldoni@valpharmaint.com", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "104308", "nombre" => "CAMARGO PHARMACEUTICAL SERVICES LLC", "nif" => "104308", "tipo" => "no stock", "contacto" => "Holly Thompson", "email" => "HollyThompson@camargopharma.com", "idioma" => "EN", "empleado" => "E0058", "responsable" => "Ingrid Chasan"),
            (object)array("codigo" => "102514", "nombre" => "INTERWOR-TSIC", "nif" => "B63950737", "tipo" => "no stock", "contacto" => "Jaume Vera", "email" => "jvera@interwor-tsic.com", "idioma" => "ES", "empleado" => "E0508", "responsable" => "Alejandro Tamayo"),
            (object)array("codigo" => "101611", "nombre" => "EUROFINS BIOPHARMA PRUCT.TEST.S.SLU", "nif" => "B63407902", "tipo" => "no stock", "contacto" => "Elena Frade", "email" => "ElenaFrade@eurofins.com", "idioma" => "ES", "empleado" => "E0077", "responsable" => "Sergio Sanllorente"),
            (object)array("codigo" => "103932", "nombre" => "R.C. ELECTRONICA, SL", "nif" => "B58826736", "tipo" => "no stock", "contacto" => "Debora Allasio", "email" => "d.allasio@rcelectronica.com", "idioma" => "ES", "empleado" => "E0508", "responsable" => "Alejandro Tamayo"),
            (object)array("codigo" => "101417", "nombre" => "EXEQUAM, S.L.", "nif" => "B64766777", "tipo" => "no stock", "contacto" => "Rosa Solé", "email" => "rmsole@exequam.com", "idioma" => "ES", "empleado" => "E0508", "responsable" => "Alejandro Tamayo"),
            (object)array("codigo" => "104157", "nombre" => "DEVELCO PHARMA SCHWEIZ AG", "nif" => "CHE112702411", "tipo" => "stock", "contacto" => "Vito Sarmini", "email" => "v.sarmini@develco.ch", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "101594", "nombre" => "DR. M. NEWZELLA GMBH", "nif" => "DE123988831", "tipo" => "no stock", "contacto" => "Dr. Christoph Newzella", "email" => "christoph.newzella@newzella.com", "idioma" => "EN", "empleado" => "E0387", "responsable" => "Alba Marín"),
            (object)array("codigo" => "104855", "nombre" => "NEW STRATEGY IN ACTION SL", "nif" => "B66482522", "tipo" => "no stock", "contacto" => "Sergio Sentias", "email" => "ssentias@strategying.com", "idioma" => "ES", "empleado" => "E0508", "responsable" => "Alejandro Tamayo"),
            (object)array("codigo" => "101615", "nombre" => "LD EMP. LIMPIEZA Y DESINFECCION. SA", "nif" => "A08426108", "tipo" => "no stock", "contacto" => "Marga Valls", "email" => "mvalls@ldfacility.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101239", "nombre" => "BRAUN MEDICAL, S.A", "nif" => "A08092744", "tipo" => "stock", "contacto" => "Marta Fernandez", "email" => "marta.fernandez@bbraun.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "101168", "nombre" => "OC VIGILANCE, S.L.", "nif" => "B99355562", "tipo" => "no stock", "contacto" => "María López", "email" => "maria.lopez@ocvigilance.com", "idioma" => "ES", "empleado" => "E0021", "responsable" => "Maria Aliaño"),
            (object)array("codigo" => "100622", "nombre" => "SCHARLAB, S.L.", "nif" => "B63048540", "tipo" => "no stock", "contacto" => "Rafa Terciado", "email" => "rafa.terciado@scharlab.com", "idioma" => "ES", "empleado" => "E0077", "responsable" => "Sergio Sanllorente"),
            (object)array("codigo" => "103066", "nombre" => "DHL FREIGHT SPAIN, S.L.U.", "nif" => "B84913433", "tipo" => "no stock", "contacto" => "Lourdes Henarejos", "email" => "lourdes.henarejos@dhl.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "100948", "nombre" => "FUND.ESP.DE REUMATOLOGIA", "nif" => "G82449323", "tipo" => "no stock", "contacto" => "Jose Carlos Jimenez/ Mª Carmen Garcia-UniontoursJose Carlos Jimenez / Mª Carmen Garcia-UniontoursJose Carlos Jimenez / Mª Carmen Garcia-Uniontours", "email" => "josecarlos.jimenez@ser.es; uniontours@uniontours.es", "idioma" => "ES", "empleado" => "E0058", "responsable" => "Ingrid Chasan"),
            (object)array("codigo" => "103199", "nombre" => "AIRPHARM SA", "nif" => "A58652892", "tipo" => "no stock", "contacto" => "Adrian Capelan", "email" => "Adrian.Capelan@airpharm.com", "idioma" => "ES", "empleado" => "E0496", "responsable" => "Núria Duran"),
            (object)array("codigo" => "101617", "nombre" => "J&A GARRIGUES, S.L.P.", "nif" => "B81709081", "tipo" => "no stock", "contacto" => "Esther Vidal", "email" => "Esther.Vidal@garrigues.com", "idioma" => "ES", "empleado" => "E0222", "responsable" => "Toni Felís"),
            (object)array("codigo" => "100016", "nombre" => "ACTOVER CO.LTD.", "nif" => "380196", "tipo" => "no stock", "contacto" => "Sherezade Quijano", "email" => "Sherezade.quijano@gmail.com", "idioma" => "EN", "empleado" => "E0496", "responsable" => "Nuria Duran"),
            (object)array("codigo" => "103663", "nombre" => "LENS CABRERA JUAN CARLOS", "nif" => "50408915E", "tipo" => "no stock", "contacto" => "Carlos Lens Cabrera", "email" => "carlos.lenscabrera@gmail.com", "idioma" => "ES", "empleado" => "E0222", "responsable" => "Toni Felís"),
            (object)array("codigo" => "104742", "nombre" => "VMLY&RX, S.L.U.", "nif" => "B81675308", "tipo" => "no stock", "contacto" => "Itziar Fraile", "email" => "Itziar.fraile@vmlyr.com", "idioma" => "ES", "empleado" => "E0514", "responsable" => "Montserrat Aguilar"),
            (object)array("codigo" => "103992", "nombre" => "IQVIA LTD", "nif" => "GB450315485", "tipo" => "no stock", "contacto" => "Machín, Jose María", "email" => "josemaria.machin@iqvia.com", "idioma" => "EN", "empleado" => "E0166", "responsable" => "Alba Marín"),
            (object)array("codigo" => "101426", "nombre" => "KIMICA CORPORATION, OVERSEAS DEPT.", "nif" => "101426", "tipo" => "stock", "contacto" => "Ishihara Masami", "email" => "ishihara-m@kimica.jp", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "100000", "nombre" => "FARMA-DERMA S.R.L.", "nif" => "IT01691071201", "tipo" => "stock", "contacto" => "Mara Calzolari", "email" => "mara.calzolari@farmaderma.it", "idioma" => "EN", "empleado" => "E0027", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "101016", "nombre" => "OFSA", "nif" => "A46377412", "tipo" => "no stock", "contacto" => "Nuria Rodriguez", "email" => "nrodriguez@cofares.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "100074", "nombre" => "QUALITY CHEMICALS, S.L.", "nif" => "B61403242", "tipo" => "stock", "contacto" => "Miriam Salvador", "email" => "msalvador@qualitychemicals.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "104738", "nombre" => "GESTIO ORGANITZACIO COMUNICACIO SA", "nif" => "A08989832", "tipo" => "no stock", "contacto" => "Carlos Marsal", "email" => "ventas@goc.es", "idioma" => "ES", "empleado" => "E0058", "responsable" => "Ingrid Chasan"),
            (object)array("codigo" => "104356", "nombre" => "ARINSO IBERICA SA", "nif" => "A82099839", "tipo" => "no stock", "contacto" => "Pere Corella", "email" => "pere.corella@alight.com", "idioma" => "ES", "empleado" => "E0508", "responsable" => "Alejandro Tamayo"),
            (object)array("codigo" => "101037", "nombre" => "VIAJES EROSKI, S.A.", "nif" => "A48115638", "tipo" => "no stock", "contacto" => "Miren Itxaso Gregorio Zarate", "email" => "miren_itxaso_gregorio@travelair-empresas.com", "idioma" => "ES", "empleado" => "E0058", "responsable" => "Ingrid Chasan"),
            (object)array("codigo" => "101068", "nombre" => "IBERICA GRAFICA, S.A.", "nif" => "A58099680", "tipo" => "stock", "contacto" => "Antonio García", "email" => "antonio@ibericagrafica.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "102752", "nombre" => "SPRIM GLOBAL PARTNERS SL", "nif" => "B85391456", "tipo" => "no stock", "contacto" => "Alicia Dordelly", "email" => "alicia.dordelly@sprim.com", "idioma" => "ES", "empleado" => "E0514", "responsable" => "Montserrat Aguilar"),
            (object)array("codigo" => "101310", "nombre" => "ALD AUTOMOTIVE, S.A.", "nif" => "A80292667", "tipo" => "no stock", "contacto" => "Davinia MANZANERA", "email" => "davinia.manzanera@aldautomotive.com", "idioma" => "ES", "empleado" => "E0387", "responsable" => "Alba Marín"),
            (object)array("codigo" => "100815", "nombre" => "DRONAS 2002, S.L.", "nif" => "B62745765", "tipo" => "no stock", "contacto" => "Joaquim Farrés", "email" => "jfarres@integra2.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "101463", "nombre" => "AMCOR FLEXIBLES SINGEN GM BH", "nif" => "DE811178054", "tipo" => "stock", "contacto" => "Heike Kern", "email" => "Heike.Kern@amcor.com", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101495", "nombre" => "AUTAJON LABELS S.A.U.", "nif" => "A60914090", "tipo" => "stock", "contacto" => "Domi Arador", "email" => "Domi.Arador@autajonlabels.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101496", "nombre" => "EMBAMAT EU, S.L.", "nif" => "B58069204", "tipo" => "no stock", "contacto" => "Xavier Garcia", "email" => "xgarcia@embamat.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "100994", "nombre" => "INDUKERN S.A.", "nif" => "A08135055", "tipo" => "stock", "contacto" => "Antonio Aizpunt", "email" => "aaizpun@indukern.es", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "102890", "nombre" => "MEDIS EHF.", "nif" => "IS22009", "tipo" => "no stock", "contacto" => "César Moreno Lerma", "email" => "cesarm@medis.is", "idioma" => "EN", "empleado" => "E0186", "responsable" => "Alejandra Pedraza"),
            (object)array("codigo" => "103192", "nombre" => "KLINEA INGENIERIA FARMACEUTICA SL", "nif" => "B66622630", "tipo" => "no stock", "contacto" => "Carles Salcedo Roca", "email" => "csalcedo@klinea.eu", "idioma" => "ES", "empleado" => "E0328", "responsable" => "Silvia Casellas"),
            (object)array("codigo" => "104131", "nombre" => "METROHM HISPANIA SLU", "nif" => "B88334131", "tipo" => "no stock", "contacto" => "Almudena Trapero", "email" => "almudena.trapero@metrohm.es", "idioma" => "ES", "empleado" => "E0077", "responsable" => "Sergio Sanllorente"),
            (object)array("codigo" => "101098", "nombre" => "ECOLOGIC I TEXTIL, S.L.", "nif" => "B43429109", "tipo" => "no stock", "contacto" => "Ivan López", "email" => "ilopez@ecotex.biz", "idioma" => "ES", "empleado" => "E0377", "responsable" => "Gemma Giraut"),
            (object)array("codigo" => "101659", "nombre" => "ECOLOGICA IBERICA Y MEDITERRANEA,S.", "nif" => "A58565755", "tipo" => "no stock", "contacto" => "María Capablo Felices", "email" => "maria.capablo@tradebe.com", "idioma" => "ES", "empleado" => "E0092", "responsable" => "Mónica Moraleda"),
            (object)array("codigo" => "104142", "nombre" => "FTF PHARMA PVT.LTD.", "nif" => "104142", "tipo" => "no stock", "contacto" => "Ganeshchandra Sonavane", "email" => "ganeshchandra.sonavane@ftfpharma.com", "idioma" => "EN", "empleado" => "E0450", "responsable" => "Miguel Angel García"),
            (object)array("codigo" => "100615", "nombre" => "MES GRAFIC, S.L.", "nif" => "B62347356", "tipo" => "no stock", "contacto" => "Ramón Arpa", "email" => "ramon@mesgrafic.com", "idioma" => "ES", "empleado" => "E0514", "responsable" => "Montserrat Aguilar"),
            (object)array("codigo" => "101406", "nombre" => "FIU-FUNDACION INVESTIG. EN UROLOGIA", "nif" => "G80445661", "tipo" => "no stock", "contacto" => "Elena Sanz", "email" => "elenasanz@aeu.es; secretariatecnica@aeu.es", "idioma" => "ES", "empleado" => "E0058", "responsable" => "Ingrid Chasan"),
            (object)array("codigo" => "103333", "nombre" => "ALIANCE-BROTHER, SL", "nif" => "B58296955", "tipo" => "no stock", "contacto" => "Vanessa Martínez", "email" => "barcelona@trasnportesllanos.com", "idioma" => "ES", "empleado" => "E0186", "responsable" => "Elisabet Ros"),
            (object)array("codigo" => "100015", "nombre" => "LABORATORIO ECHEVARNE, S.A.", "nif" => "A08829848", "tipo" => "no stock", "contacto" => "Proveedores", "email" => "proveedores.mm@laboratorioechevarne.com", "idioma" => "ES", "empleado" => "E0077", "responsable" => "Sergio Sanllorente"),
        );
    }
}
