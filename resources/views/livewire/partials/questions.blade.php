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
                    <label for="numbers_format">Sección*:</label>
                    <select {{ $this->formEdit ? '' : 'disabled' }} wire:model.defer="sectionQuestionSelected"
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
        </div>
    </div>

    <div class="col-md-4 col-12 mt-3">
        <div class="form-group ">
            <label for="numbers_format">Preguntas por defecto:</label>
            <div class="d-flex">
                <select {{ $this->formEdit ? '' : 'disabled' }} wire:model.defer="selectedDefaultQuestion"
                    class="form-control " id="numbers_format_input">
                    <option value="">Selecciona una pregunta por defecto</option>
                    @foreach ($this->defaultQuestions as $question)
                    <option value="{{ $question->id }}">
                        {{ $question->code }} - {{ $question->getTranslation('content', 'es') }} - {{
                        $typeAnwers[$question->type] }}
                    </option>
                    @endforeach
                </select>
                <button wire:click="addDefaultQuestion" class="btn btn-dark" type="button" title="Añadir">+</button>
            </div>

        </div>

    </div>

    <div class="col-md-12 mt-3">
        <div class="form-group">
            <label class="form-control-label" for="input-first_name">Pregunta*</label>
            <nav id="create-questions">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-question-tab" data-toggle="tab" href="#nav-question-es"
                        role="tab" aria-controls="nav-home" aria-selected="true">Español</a>
                    <a class="nav-item nav-link" id="nav-question-tab" data-toggle="tab" href="#nav-question-en"
                        role="tab" aria-controls="nav-profile" aria-selected="false">Ingles</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-question-es" role="tabpanel"
                    aria-labelledby="nav-home-tab">
                    <input disabled wire:model.defer="questionName.es" type="text" name="section-name"
                        id="question-name-es" class="form-control form-control-alternative"
                        placeholder="Introduzca nombre">
                </div>
                <div class="tab-pane fade" id="nav-question-en" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <input disabled wire:model.defer="questionName.en" type="text" name="section-name"
                        id="question-name-en" class="form-control form-control-alternative"
                        placeholder="Introduzca nombre">
                </div>
            </div>
            @error('questionName.*')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-12">
        <div class="form-group mb-2">
            <label for="numbers_format">Tipo*:</label>
            <select disabled wire:model.live="typeSelected" class="form-control " id="numbers_format_input" size="3">
                @foreach ($typeAnwers as $key => $value)
                <option value="{{ $key }}">
                    {{ $value }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <input {{ $this->typeSelected != 'radio' ? 'disabled' : '' }} type="checkbox"
            wire:model.defer="question.comments">
            <label for="numbers_format">Comentarios</label>
        </div>
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
        <div class="form-group mt-n3">
            <input type="checkbox" wire:model.defer="requiredQuestion">
            <label for="numbers_format">Obrigatoria</label>
        </div>
    </div>
</div>
@if($customOptions)
<div class="row  mb-3">
    <div class="col-md-12 mt-n2">
        <label class="form-control-label" for="input-first_name">Opciónes</label>
        <table class="table table-striped table-bordered table-hover table-checkable">
            <thead>
                <tr>
                    <th style="width: 50%">ES</th>
                    <th style="width: 50%">EN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($optionES as $keyOption => $option)
                <tr>
                    <td>{{ $option }}</td>
                    <td>{{ $optionEN[$keyOption] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@if ($this->formEdit)
<div class="row justify-content-end mb-4">
    <div class="col-4">
        <div class="text-right" aria-label="">
            @if (!empty($this->question->id))
            <button type="button" wire:click="resetValues" class="btn btn-sm btn-dark mr-2"
                wire:loading.attr="disabled">
                Cancelar
            </button>
            <button type="button" wire:click="saveQuestion" class="btn btn-sm btn-info" wire:loading.attr="disabled">
                Guardar Pregunta
            </button>
            @endif
        </div>
    </div>
</div>
@endif
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-bordered table-hover table-checkable">
            <thead>
                <tr>
                    <th>Acción</th>
                    <th>Sección</th>
                    <td>Orden</td>
                    <th class="col-1">Codigo</th>
                    <th class="col-5">ES</th>
                    <th class="col-5">EN</th>
                    <th class="col-1">Tipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->survey->surveyQuestionsMain as $item)
                <tr>
                    <td nowrap>
                        @if($survey->status !== 0)
                        <button wire:loading.delay.attr="disabled" wire:target="downloadExcel" {{ $this->formEdit ? '' :
                            'disabled' }}
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
                        @else
                        @if($this->isActive($item->id))
                        <button type="button" {{ $this->formEdit ? '' : 'disabled' }}
                            wire:click="activeQuestion('{{ $item->id }}')"
                            class="btn btn-xl btn-clean btn-icon btn-icon-xl" style="width:100%; height:100%;"
                            title="Pulsa para Desactivar">
                            <i class="fas fa-toggle-on fa-xl" aria-hidden="true"></i>
                            @else
                            <button type="button" {{ $this->formEdit ? '' : 'disabled' }}
                                wire:click="activeQuestion('{{ $item->id }}')"
                                class="btn btn-xl btn-clean btn-icon btn-icon-xl" style="width:100%; height:100%;"
                                title="Pulsa para Activar">
                                <i class="fas fa-toggle-off fa-xl" aria-hidden="true"></i>
                                @endif
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
                    <td>
                        {{ $item->question->getTranslation('content', 'en') }}
                    </td>
                    <td>
                        {{ $typeAnwers[$item->question->type] ?? $item->question->type }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@include('survey::livewire.partials.subquestions')