<div>
    @if(empty($survey->type))
    <div class="row mt-2">
        <div class="col-12">
            <label class="form-control-label" for="input-key_value">Seleccione el tipo de formulario: </label>
            <div>
                @foreach($surveyTypes as $type)
                <button type="button" wire:click="$set('survey.type','{{$type->id}}')" wire:loading.attr="disabled"
                    class="btn btn-sm btn-primary d-flex p-4 py-lg-2 float-left mr-2">
                    {{$type->name}}
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div>
        <div class="row mt-2">
            <div class="col-12 col-md-6 py-2">
                <h4>Formulario {{ $this->survey->survey_number ?? ''}} de {{ $this->survey->surveyType->name ?? ''}} @if($projectCode != "") del proyecto {{$projectCode}}  @endif</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @if (session()->has('alert'))
                <div class="alert alert-danger">
                    {{ session('alert') }}
                </div>
                @endif
                @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link  active" id="survey-header-tab" data-toggle="tab" href="#survey-header"
                            role="tab" aria-controls="survey-header" aria-selected="true">Cabecera</a>
                    </li>
                    @if($projectCode != "")
                    <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link {{ is_null($this->survey->id) ? 'disabled'
                            : ''}}" id="survey-questions-tab" data-toggle="tab" href="#survey-questions" role="tab"
                            aria-controls="survey-questions" aria-selected="true">Preguntas</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link {{ is_null($this->survey->id) ? 'disabled'
                            : ''}}" id="survey-preview-tab" data-toggle="tab" href="#survey-preview" role="tab"
                            aria-controls="survey-preview" aria-selected="true">Previsualizar</a>
                    </li>
                    @endif
                    {{-- <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link {{ is_null($this->survey->id) ? 'disabled'
                            : ''}}" id="survey-users-tab" data-toggle="tab" href="#survey-users" role="tab"
                            aria-controls="survey-users" aria-selected="true">Destinatarios</a>
                    </li> --}}
                    {{-- <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link {{ is_null($this->survey->id) ? 'disabled'
                            : ''}}" id="survey-chat-tab" data-toggle="tab" href="#survey-chat" role="tab"
                            aria-controls="survey-chat" aria-selected="true">Chat</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link {{ is_null($this->survey->id) ? 'disabled'
                            : ''}}" id="survey-files-tab" data-toggle="tab" href="#survey-files" role="tab"
                            aria-controls="survey-files" aria-selected="true">Archivos</a>
                    </li> --}}
                    {{-- <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link {{ is_null($this->survey->id) ? 'disabled'
                            : ''}}" id="survey-audit-tab" data-toggle="tab" href="#survey-audit" role="tab"
                            aria-controls="survey-audit" aria-selected="true">Auditoría</a>
                    </li> --}}
                </ul>
                <div class="tab-content" id="orderContent">
                    <div class="tab-pane fade show active" id="survey-header" role="tabpanel"
                        aria-labelledby="survey-header-tab" wire:ignore.self>
                        @include('survey::livewire.partials.header')
                    </div>
                    @if($projectCode != "")
                    @if($this->survey->id)
                    <div class="tab-pane fade" id="survey-questions" role="tabpanel" aria-labelledby="survey-questions"
                        wire:ignore.self>
                        @include('survey::livewire.partials.questions')
                    </div>
                    <div class="tab-pane fade" id="survey-preview" role="tabpanel" aria-labelledby="survey-preview"
                        wire:ignore.self>
                        @include('survey::standard', ['survey' => $survey,
                        'sendForm' => false,
                        'disabled' => true])
                    </div>
                    @endif
                    {{-- <div class="tab-pane fade" id="survey-users" role="tabpanel" aria-labelledby="survey-users"
                        wire:ignore.self>
                        @livewire('iworking-survery::addresses',[
                        'survey' => $this->survey->id
                        ])
                    </div> --}}
                    {{-- <div class="tab-pane fade" id="survey-chat" role="tabpanel" aria-labelledby="survey-chat"
                        wire:ignore.self>
                        @livewire('iworking::common-comments',[
                        'entityId' => $survey->id,
                        'entityType' => 'App\Models\Survey',
                        'textTitle' => 'Chat interno',
                        'editable' => true
                        ])
                    </div>
                    <div class="tab-pane fade" id="survey-files" role="tabpanel" aria-labelledby="survey-files"
                        wire:ignore.self>
                        @livewire('common.file-upload', [
                        's3' => true,
                        'path' => config('custom.iworking_public_bucket_folder_surveys') .
                        '/' . now()->format('Y/m/d') . '/' . (string)$survey->id,
                        'modelId' => (string)$survey->id,
                        'model' => 'Models\Survey::class',
                        'type' => 'surveys-attachment',
                        'enableUpload' => true,
                        'enableDelete' => true,
                        ], key(time() . 'file-uploader'))
                    </div> --}}
                    {{-- <div class="tab-pane fade" id="survey-audit" role="tabpanel" aria-labelledby="survey-audit"
                        wire:ignore.self>
                        @livewire('iworking::common-audit-table',[
                        'dataValue' => $survey,
                        'nameStatus' => 'surveys'
                        ])
                    </div> --}}
                    @endif
                </div>
            </div>
        </div>
        <hr>
        @if (session()->has('userListEmpty'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <span> {{ session('userListEmpty') }}</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if($this->formEdit)
        <div class="row">
            <div class="col-12 col-md-6">

            </div>
            <div class="col-12 col-md-6">
                <div class="text-right">
                    <div class="btn-group my-1" role="group" aria-label="">
                        {{-- @if($this->survey->status == MattDaneshvar\Survey\Library\Constants::SURVEY_STATUS_DRAFT &&
                        $this->survey->author == auth()->user()->id)
                        <button type="button" wire:click="deleteSurvey"
                            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
                            class="btn btn-sm btn-danger d-flex p-4 py-lg-2 mr-2" wire:loading.attr="disabled">
                            Eliminar
                        </button>
                        @endif --}}
                        @if(is_null($this->survey->id))
                        <button type="button" wire:click="saveSurvey"
                            class="btn btn-sm btn-success d-flex p-4 py-lg-2 mr-2" wire:loading.attr="disabled">
                            Crear formulario
                        </button>
                        @else
                        <button type="button" wire:click="saveSurvey"
                            class="btn btn-sm btn-success d-flex p-4 py-lg-2 mr-2" wire:loading.attr="disabled">
                            Guardar borrador
                        </button>
                        {{-- <button class="btn btn-warning" {{ $this->survey->questions->count() ?
                            '' : 'disabled'}}
                            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') ||
                            event.stopImmediatePropagation();"
                            wire:click="sendSurvey">
                            Enviar
                        </button> --}}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if($this->survey->status == MattDaneshvar\Survey\Library\Constants::SURVEY_STATUS_PROCESS &&
        auth()->user()->hasAnyRole(['gestor-encuestas']))
        <div class="row">
            <div class="col-12 col-md-6">
            </div>
            <div class="col-12 col-md-6">
                <div class="text-right">
                    <div class="btn-group my-1" role="group" aria-label="">
                        <button type="button" wire:click="closeSurvey"
                            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
                            class="btn btn-dark d-flex p-4 py-lg-2 mr-2" wire:loading.attr="disabled">
                            Cerrar formulario
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>