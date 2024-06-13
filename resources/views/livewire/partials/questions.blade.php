@if (session()->has('questionSaved'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span> {{ session('questionSaved') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@if (session()->has('questionWarning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <span> {{ session('questionWarning') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<div class="row">
    <div class="col-12">
        <h5>
            Preguntas
        </h5>
    </div>
    <div class="col-12">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="form-group ">
                    <label for="numbers_format">Etapa*:</label>
                    <select wire:model.live="sectionQuestionSelected"
                        class="form-control "
                        id="numbers_format_input" size="3">
                        @foreach ($this->survey->sections as $section)
                        <option value="{{ $section->id }}">
                            {{ $section->order }} - {{ $section->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('sectionQuestionSelected')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            {{-- <div class="col-12 col-md-6">
                <div class="row justify-content-end mb-4">
                    <div class="text-right" aria-label="">
                        <button wire:click="refreshQuestions" class="btn btn-secondary btn-elevate btn-sm"
                            target="_blank">Recargar Preguntas</button>
                        <a href="{{ route('questions.new') }}" class="btn btn-success btn-elevate btn-sm"
                            target="_blank"> + Nueva Pregunta</a>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
    
    @if($this->formEdit)
    <div class="col-12">
        <div class="card-header mb-5">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex align-items-center"
                    style="gap: 15px; text-decoration: none !important;" data-toggle="collapse"
                    data-target="#collapseQuestionsAdd" aria-expanded="true" aria-controls="collapseQuestionsAdd">
                    <span class="h3">Crear pregunta a etapa:</span>
                    <i class="fas fa-chevron-up tab-arrow"></i>
                </button>
            </h5>
        </div>
        <div id="collapseQuestionsAdd" class="collapse col-12 card-body" aria-labelledby="headingTwo"
            data-parent="#accordion" wire:ignore.self>
            {{-- <div class="col-md-4 col-12 mt-3">
                <div class="form-group ">
                    <label for="numbers_format">Preguntas por defecto:</label>
                    <div class="d-flex">
                        <select {{ $this->formEdit ? '' : 'disabled' }} @if(empty($sectionQuestionSelected))
                            disabled @endif wire:model.defer="selectedDefaultQuestion"
                            class="form-control " id="numbers_format_input">
                            <option value="">Selecciona una pregunta por defecto</option>
                            @foreach ($this->defaultQuestions as $question)
                            <option value="{{ $question->id }}">
                                {{ $question->code }} - {{ $question->getTranslation('content', 'es') }} - {{
                                $typeAnwers[$question->type] }}
                            </option>
                            @endforeach
                        </select>
                        <button wire:click="addDefaultQuestion" class="btn btn-dark" type="button"
                            title="Añadir">+</button>
                    </div>
                </div>
            </div> --}}
            <div class="col-md-12 mt-3">
                <div class="form-group">
                    <label class="form-control-label" for="input-first_name">Pregunta*</label>
                    {{-- <nav id="create-questions">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-question-tab" data-toggle="tab"
                                href="#nav-question-es" role="tab" aria-controls="nav-home"
                                aria-selected="true">Español</a>
                            <a class="nav-item nav-link" id="nav-question-tab" data-toggle="tab" href="#nav-question-en"
                                role="tab" aria-controls="nav-profile" aria-selected="false">Ingles</a>
                        </div>
                    </nav> --}}
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-question-es" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <input wire:model.defer="questionName.es" type="text" name="section-name"
                                id="question-name-es" class="form-control form-control-alternative"
                                placeholder="Introduzca nombre">
                        </div>
                        {{-- <div class="tab-pane fade" id="nav-question-en" role="tabpanel"
                            aria-labelledby="nav-profile-tab">
                            <input disabled wire:model.defer="questionName.en" type="text" name="section-name"
                                id="question-name-en" class="form-control form-control-alternative"
                                placeholder="Introduzca nombre">
                        </div> --}}
                    </div>
                    @error('questionName.*')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-5 col-12">
                <div class="form-group mb-2">
                    <label for="numbers_format">Tipo*:</label>
                    <select wire:model.live="typeSelected" class="form-control " id="numbers_format_input" size="3">
                        @foreach ($typeAnwers as $key => $value)
                        <option value="{{ $key }}">
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group">
                    <input {{ $this->typeSelected != 'radio' ? 'disabled' : '' }} type="checkbox"
                    wire:model.defer="question.comments">
                    <label for="numbers_format">Comentarios</label>
                </div> --}}
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="numbers_format">Orden:</label>
                    <input {{ $this->formEdit ? '' : 'disabled' }} wire:model.defer="orderQuestion" type="number"
                    step="1" min="0" name="section-order" id="survey-order"
                    class="form-control form-control-alternative" placeholder="Orden">
                    @error('orderQuestion')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="form-group col-12 col-md-6 col-xl-4 mt-n3">
                        <input type="checkbox" @if($indicatedQuestion || $targetQuestion) disabled checked @endif
                            wire:model.live="requiredQuestion">
                        <label for="numbers_format">Obligatoria</label>
                    </div>
                    <div class="form-group col-12 col-md-6 col-xl-4 mt-n3">
                        <input type="checkbox" @if($indicatedQuestion) checked @endif
                            wire:model.live="indicatedQuestion">
                        <label for="numbers_format">Indicador</label>
                    </div>
                    <div class="form-group col-12 col-md-6 col-xl-4 mt-n3">
                        <input type="checkbox" @if($targetQuestion) checked @endif wire:model.live="targetQuestion">
                        <label for="numbers_format">Objetivo</label>
                    </div>
                </div>
            </div>
            @if($targetQuestion)
            <div class="col-12 mb-3 mt-n2">
                <div class="form-group">
                    <label class="form-control-label" for="input-target">Objetivo de la pregunta*</label>
                    <select class="form-control " wire:model.defer="targetSelected" id="input-target">
                        <option value="">Selecciona un objetivo</option>
                        @foreach ($targets as $target)
                        <option value="{{ $target['id'] }}">
                            {{ $target['name'] }}
                        </option>
                        @endforeach
                    </select>
                    @error('targetSelected.*')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @endif
            @if($customOptions)
            <div class="col-md-12 mt-n2">
                <lable class="h5">Opciones de la pregunta</lable>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-control-label" for="input-first_name">Opción</label>
                <div class="row">
                    <div class="col-10">
                        <input type="text" wire:model.defer="newOptionES" class="form-control form-control-alternative"
                            placeholder="Introduzca opción">
                    </div>
                    <div class="col-2">
                        <button wire:click="addOption" class="btn btn-dark" type="button" title="Añadir">+</button>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <label class="form-control-label" for="input-first_name">Opciónes</label>
                <table class="table table-striped table-bordered table-hover table-checkable">
                    <thead>
                        <tr>
                            <th style="width: 15%">Opciones</th>
                            <th style="width: 85%">ES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($optionES as $keyOption => $option)
                        <tr>
                            <td>
                                <button wire:loading.attr="disabled" wire:click="editOption({{ $keyOption }})"
                                    type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                                    data-target="#delete_modal" data-toggle="tooltip" data-placement="top"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" {{ $this->formEdit ? '' : 'disabled'}}
                                    onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') ||
                                    event.stopImmediatePropagation();"
                                    wire:click="deleteOption({{ $keyOption }})"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Delete">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                                <button wire:loading.attr="disabled" wire:click="upOption({{ $keyOption }})"
                                    type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                                    data-target="#delete_modal" data-toggle="tooltip" data-placement="top"
                                    title="Subir orden">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <button wire:loading.attr="disabled" wire:click="downOption({{ $keyOption }})"
                                    type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                                    data-target="#delete_modal" data-toggle="tooltip" data-placement="top"
                                    title="Bajar Orden">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                            </td>
                            <td>{{ $option }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            @if ($this->formEdit)
            <div class="row justify-content-end mb-4">
                <div class="col-4">
                    <div class="text-right" aria-label="">
                        <button type="button" wire:click="resetValues" class="btn btn-sm btn-dark mr-2"
                            wire:loading.attr="disabled">
                            Cancelar
                        </button>
                        <button type="button" wire:click="saveQuestion" class="btn btn-sm btn-info"
                            wire:loading.attr="disabled">
                            Guardar Pregunta
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
<div class="row">
    <div class="col-12">
        <div class="card-header col-12 mb-5">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex align-items-center"
                    style="gap: 15px; text-decoration: none !important;" data-toggle="collapse"
                    data-target="#collapseQuestions" aria-expanded="true" aria-controls="collapseQuestions">
                    <span class="h3">Preguntas:</span>
                    <i class="fas fa-chevron-up tab-arrow"></i>
                </button>
            </h5>
        </div>
        <div id="collapseQuestions" class="collapse show col-12 card-body" aria-labelledby="headingTwo"
            data-parent="#accordion" wire:ignore.self>
            <table class="table table-striped table-bordered table-hover table-checkable">
                <thead>
                    <tr>
                        <th>Acción</th>
                        <th>Etapa</th>
                        <td>Orden</td>
                        <th>Codigo</th>
                        <th class="col-5">Pregunta</th>
                        {{-- <th class="col-5">EN</th> --}}
                        <th>Tipo</th>
                        <th>Obligatoria</th>
                        <th>Indicador</th>
                        <th>Objetivo</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($sectionQuestionSelected))
                    @foreach ($survey->sections()->find($sectionQuestionSelected)->surveyQuestionsMainIgnoreDisabled()->get()->sortBy('position') as $key => $item)
                    <tr>
                        <td nowrap>
                            @if($this->formEdit)
                            <button wire:loading.delay.attr="disabled" wire:target="downloadExcel" 
                                wire:click="editQuestion('{{ $item->id }}')" type="button"
                                class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                data-placement="top" title="Edit">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </button>
                            <button type="button" {{ $this->formEdit ? '' : 'disabled' }}
                                onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') ||
                                event.stopImmediatePropagation();"
                                wire:click="deleteQuestion('{{ $item->id }}')"
                                class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Delete">
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>

                            <button wire:loading.attr="disabled" wire:click="upQuestion('{{ $item->id }}')"
                                type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                                data-toggle="tooltip" data-placement="top" title="Subir orden">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button wire:loading.attr="disabled" wire:click="downQuestion('{{ $item->id }}')"
                                type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                                data-toggle="tooltip" data-placement="top" title="Bajar Orden">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            @endif
                            @if($this->isActive($item->id))
                            <button type="button"
                                wire:click="activeQuestion('{{ $item->id }}')"
                                class="btn btn-sm btn-clean btn-icon btn-icon-md"
                                title="Pulsa para Desactivar">
                                <i class="fas fa-toggle-on fa-xl" aria-hidden="true"></i>
                            </button>
                            @else
                            <button type="button"
                                wire:click="activeQuestion('{{ $item->id }}')"
                                class="btn btn-sm btn-clean btn-icon btn-icon-md"
                                title="Pulsa para Activar">
                                <i class="fas fa-toggle-off fa-xl" aria-hidden="true"></i>
                            </button>
                            @endif

                        </td>
                        <td>
                            {{ $item->section->name ?? '' }}
                        </td>
                        <td>
                            {{ $item->position }}
                        </td>
                        <td>
                            {{ $item->question->code ?? '' }}
                        </td>
                        <td>
                            {{ $item->question->getTranslation('content', 'es') }}
                        </td>
                        {{-- <td>
                            {{ $item->question->getTranslation('content', 'en') }}
                        </td> --}}
                        <td>
                            {{ $typeAnwers[$item->question->type] ?? $item->question->type }}
                        </td>
                        <td>
                            {{ $item->mandatory ? 'Si':'No' }}
                        </td>
                        <td>
                            {{ $item->indicated ? 'Si':'No' }}
                        </td>
                        <td>
                            {{ $item->target ? 'Si':'No' }}
                        </td>
                        
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('survey::livewire.partials.subquestions')