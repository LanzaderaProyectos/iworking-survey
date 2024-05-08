@if (session()->has('draftSurveyCreated'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span> {{ session('draftSurveyCreated') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@if (session()->has('surveyUpdated'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span> {{ session('surveyUpdated') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@if (session()->has('survey-expiration-updated'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span> {!! session('survey-expiration-updated') !!}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="row">
    <div class="col-12 col-md-6">
        <div class="form-group">
            <label class="form-control-label" for="input-first_name">Nombre*</label>
            {{-- <nav id="survey-name-options">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-survey-es-tab" data-toggle="tab" href="#nav-survey-es"
                        role="tab" aria-controls="nav-survey-es" aria-selected="true">Español</a>
                    <a class="nav-item nav-link" id="nav-survey-en-tab" data-toggle="tab" href="#nav-survey-en"
                        role="tab" aria-controls="nav-survey-en" aria-selected="true">Ingles</a>
                </div>
            </nav> --}}
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-survey-es" role="tabpanel"
                    aria-labelledby="nav-survey-es-tab">
                    <input {{ $this->formEdit ? '' : 'disabled'}} wire:model.defer="surveyName.es" type="text"
                    name="section-name-es"
                    class="form-control form-control-alternative" placeholder="Introduzca nombre">
                </div>
                {{-- <div class="tab-pane fade" id="nav-survey-en" role="tabpanel" aria-labelledby="nav-survey-es-tab">
                    <input {{ $this->formEdit ? '' : 'disabled'}} wire:model.defer="surveyName.en" type="text"
                    name="section-name-en"
                    class="form-control form-control-alternative" placeholder="Introduzca nombre">
                </div> --}}
            </div>
            @error('surveyName.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="row">
            <div class="col-12 mt-1">
                <label class="form-control-label" for="survey.expiration">Fecha expiración*:</label>
                <input {{ $this->formEdit || ($this->survey->status ==
                MattDaneshvar\Survey\Library\Constants::SURVEY_STATUS_PROCESS &&
                auth()->user()->hasAnyRole(['gestor-encuestas'])) ? '' : 'disabled'}} type="date"
                wire:model.lazy="survey.expiration"
                class="form-control mt-n2" min="{{date("Y-m-d")}}">
                @error('survey.expiration') <span class="text-danger">{{ $message }}</span> @enderror
                @if (session()->has('survey-expiration-update'))
                <button class="mt-2 btn btn-warning btn-sm float-right"
                    onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
                    wire:click="updateExpirationSurvey">
                    Guardar y enviar recordatorio
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label for="exampleFormControlTextarea1">Comentarios</label>
            <textarea {{
                $this->formEdit ? '' : 'disabled'}} class="form-control" id="survey-comments" rows="3" wire:model.defer="survey.comments"></textarea>
        </div>
    </div>
</div>
@if($this->survey->id)

<div class="row my-5">
    <div class="col-6 col-md-3 col-xl-2">
        <div class="form-group">
            <label class="form-control-label" for="input-first_name">Tiene Comanda</label><br>
            <input type="checkbox" wire:model.defer="survey.has_order" name="has_order" id="has_order"
                class="">
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl-2">
        <div class="form-group">
            <label class="form-control-label" for="input-first_name">Tiene Material Promocional</label><br>
            <input type="checkbox" wire:model.defer="survey.has_promotional_material" name="has_order" id="has_order"
                class="">
        </div>
    </div>
</div>
<div class="row my-5">
    <div class="col-12 col-md-6">
        <div class="form-group">
            <label class="form-control-label" for="input-first_name">Nombre de la etapa</label>
            {{-- <nav id="create-sections-surveys">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-section-tab" data-toggle="tab" href="#nav-section-es"
                        role="tab" aria-controls="nav-home" aria-selected="true">Español</a>
                    <a class="nav-item nav-link" id="nav-section-tab" data-toggle="tab" href="#nav-section-en"
                        role="tab" aria-controls="nav-profile" aria-selected="false">Ingles</a>
                </div>
            </nav> --}}
            <div class="input-group">
                <div class="row">
                    <div class="col-8 pr-0">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-section-es" role="tabpanel"
                                aria-labelledby="nav-home-tab">
                                <input {{ $this->formEdit ? '' : 'disabled'}} wire:model.defer="sectionName.es"
                                type="text" name="section-name"
                                id="survey-name-es" class="form-control form-control-alternative"
                                placeholder="Introduzca nombre">
                            </div>
                            {{-- <div class="tab-pane fade" id="nav-section-en" role="tabpanel"
                                aria-labelledby="nav-profile-tab">
                                <input {{ $this->formEdit ? '' : 'disabled'}} wire:model.defer="sectionName.en"
                                type="text" name="section-name"
                                id="survey-name-en" class="form-control form-control-alternative"
                                placeholder="Introduzca nombre">
                            </div> --}}
                        </div>
                        @error('sectionName.*') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-4 p-0">
                        <input {{ $this->formEdit ? '' : 'disabled'}} wire:model.defer="section.order" type="number"
                        step="1" min="0" name="section-name"
                        id="survey-name" class="form-control form-control-alternative" placeholder="Orden">
                    </div>
                </div>
                <div class="input-group-append">
                    <button wire:click="addSection" class="btn btn-dark" type="button" title="Añadir">+</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        @if(count($this->survey->sections))
        <table class="table table-striped table-bordered table-hover table-checkable mt-5">
            <thead>
                <tr>
                    <th class="col-1">Acción</th>
                    <th class="col-1">Orden</th>
                    <td>Nombre Etapa</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->survey->sections as $section)
                <tr>
                    <td nowrap>
                        <button wire:loading.attr="disabled" wire:click="editSection('{{ $section->id }}')"
                            type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                            data-target="#delete_modal" data-toggle="tooltip" data-placement="top"
                            title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" {{ $this->formEdit ? '' : 'disabled'}}
                            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') ||
                            event.stopImmediatePropagation();"
                            wire:click="deleteSection('{{ $section->id }}')"
                            class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Delete">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                        <button wire:loading.attr="disabled" wire:click="upSection('{{ $section->id }}')" type="button"
                            class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                            data-target="#delete_modal" data-toggle="tooltip" data-placement="top"
                            title="Subir orden">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <button wire:loading.attr="disabled" wire:click="downSection('{{ $section->id }}')" type="button"
                            class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                            data-target="#delete_modal" data-toggle="tooltip" data-placement="top"
                            title="Bajar Orden">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </td>
                    <td>
                        {{ $section->order ?? '' }}
                    </td>
                    <td>
                        {{$section->getTranslation('name','es')}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endif