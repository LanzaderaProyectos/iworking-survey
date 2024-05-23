@if (session()->has('subQuestionSaved'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <span> {{ session('subQuestionSaved') }}</span>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif


<div class="row mt-5">
    <div class="col-12">
        <h5>
            Sub Preguntas
        </h5>
    </div>
    <div class="col-md-6 col-12 mt-3">
        <div class="form-group ">
            <label for="numbers_format">Selecciona una pregunta:</label>
            <select {{ $this->formEdit ? '' : 'disabled' }} wire:model.live="selectedParentQuestionId"
                class="form-control " id="numbers_format_input">
                <option value="">Seleccione una pregunta</option>
                
                @foreach ($survey->sections()->find($sectionQuestionSelected)->surveyQuestionsMain as $element)
                @if (in_array($element->question->type, ['radio','multiselect','uniqueselect']))
                <option value="{{ $element->id }}">
                    {{ $element->section->name }} - {{ $element->position }} - {{
                    $element->question->getTranslation('content', 'es') }} - {{$typeAnwers[$element->question->type]}}
                </option>
                @endif
                @endforeach
            </select>
        </div>
    </div>

    @if ($this->selectedParentQuestion)
    <div class="col-md-6 col-12 mt-3">
        <div class="form-group mb-3">
            <label for="numbers_format">¿Cuándo debe de mostrar la pregunta?*:</label>
            <select {{ $this->formEdit ? '' : 'disabled' }} @if($selectedParentQuestion->type == "multiselect" ||
                $selectedParentQuestion->type == "uniqueselect") disabled @endif wire:model.live="parentQuestionRadio"
                class="form-control " id="numbers_format_input">
                @if($selectedParentQuestion->type == "multiselect" || $selectedParentQuestion->type == "uniqueselect")
                <option value="00">En cualquier caso</option>
                @else
                <option value="">Selecciona una opción</option>
                <option value="SI">Cuando pulsa SI</option>
                <option value="NO">Cuando pulsa NO</option>
                <option value="NA">Cuando pulsa NA</option>
                <option value="00">En cualquier caso</option>
                @endif
            </select>
        </div>
    </div>
    @endif
</div>

@if ($this->selectedParentQuestion)
@if (
($this->selectedParentQuestion->type == 'radio' && $this->parentQuestionRadio != null) ||
$this->selectedParentQuestion->type != 'radio')
{{-- <div class="col-md-4 col-12 mt-3">
    <div class="form-group ">
        <label for="numbers_format">Sub pregunta por defecto:</label>
        <div class="d-flex">
            <select {{ $this->formEdit ? '' : 'disabled' }} wire:model.defer="selectedDefaultQuestionSub"
                class="form-control " id="numbers_format_input">
                <option value="">Selecciona una pregunta por defecto</option>
                @foreach ($this->defaultQuestionsSub as $question)
                <option value="{{ $question->id }}">
                    {{ $question->getTranslation('content', 'es') }} - {{ $typeAnwers[$question->type] }}
                </option>
                @endforeach
            </select>
            <button wire:click="addDefaultQuestionSub()" class="btn btn-dark" type="button" title="Añadir">+</button>
        </div>

    </div>

</div> --}}
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-control-label" for="input-first_name">Pregunta*</label>
            {{-- <nav id="create-questions">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-subquestion-tab" data-toggle="tab"
                        href="#nav-subquestion-es" role="tab" aria-controls="nav-home" aria-selected="true">Español</a>
                    <a class="nav-item nav-link" id="nav-subquestion-tab" data-toggle="tab" href="#nav-subquestion-en"
                        role="tab" aria-controls="nav-profile" aria-selected="false">Ingles</a>
                </div>
            </nav> --}}
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-subquestion-es" role="tabpanel"
                    aria-labelledby="nav-home-tab">
                    <input {{ $this->formEdit ? '' : 'disabled' }} wire:model="subQuestionName.es"
                    type="text" name="section-name" id="subquestion-name-es"
                    class="form-control form-control-alternative" placeholder="Introduzca nombre">
                </div>
                {{-- <div class="tab-pane fade" id="nav-subquestion-en" role="tabpanel"
                    aria-labelledby="nav-profile-tab">
                    <input disabled {{ $this->formEdit ? '' : 'disabled' }} wire:model="subQuestionName.en"
                    type="text" name="section-name" id="subquestion-name-en"
                    class="form-control form-control-alternative" placeholder="Introduzca nombre">
                </div> --}}
            </div>
            @error('subQuestionName.*')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-5 col-12">
        <div class="form-group mb-3">
            <label for="numbers_format">Tipo*:</label>
            <select {{ $this->formEdit ? '' : 'disabled' }} wire:model.live="subTypeSelected"
                class="form-control " id="numbers_format_input" size="3">
                @foreach ($subTypeAnwers as $key => $value)
                <option value="{{ $key }}">
                    {{ $value }}
                </option>
                @endforeach
            </select>
        </div>
        {{-- <div class="form-group">
            <input {{ $this->subTypeSelected != 'radio' ? 'disabled' : '' }} type="checkbox"
            wire:model.defer="subQuestion.comments">
            <label for="numbers_format">Comentarios</label>
        </div> --}}
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="numbers_format">Orden:</label>
            <input {{ $this->formEdit ? '' : 'disabled' }} wire:model.defer="orderSubQuestion"
            type="number" step="1" min="0" name="section-order" id="subquestion-survey-order"
            class="form-control form-control-alternative" placeholder="Orden">
            @error('orderSubQuestion')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="row">
            <div class="form-group col-12 col-md-6 col-xl-4 mt-n3">
                <input type="checkbox" @if($indicatedSubQuestion || $targetSubQuestion) disabled @endif
                    wire:model.defer="requiredSubQuestion">
                <label for="numbers_format">Obligatoria</label>
            </div>
            <div class="form-group col-12 col-md-6 col-xl-4 mt-n3">
                <input type="checkbox" wire:model.live="indicatedSubQuestion">
                <label for="numbers_format">Indicador</label>
            </div>
            <div class="form-group col-12 col-md-6 col-xl-4 mt-n3">
                <input type="checkbox" wire:model.live="targetSubQuestion">
                <label for="numbers_format">Objetivo</label>
            </div>
        </div>
    </div>
</div>
@if($subTypeSelected == "multiselect" || $subTypeSelected == "uniqueselect")
<div class="col-md-12 mt-n2">
    <label class="form-control-label" for="input-first_name">Opción</label>
    <div class="row">
        <div class="col-10">
            <input type="text" wire:model.defer="newSubOptionES" class="form-control form-control-alternative"
                placeholder="Introduzca opción">
        </div>
        <div class="col-2">
            <button wire:click="addSubOption" class="btn btn-dark" type="button" title="Añadir">+</button>
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
            @foreach($subOptionEs as $keyOption => $option)
            <tr>
                <td>
                    <button wire:loading.attr="disabled" wire:click="editSubOption({{ $keyOption }})" type="button"
                        class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                        data-target="#delete_modal" data-toggle="tooltip" data-placement="top" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" {{ $this->formEdit ? '' : 'disabled'}}
                        onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') ||
                        event.stopImmediatePropagation();"
                        wire:click="deleteSubOption({{ $keyOption }})"
                        class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Delete">
                        <i class="fas fa-trash" aria-hidden="true"></i>
                    </button>
                    <button wire:loading.attr="disabled" wire:click="upSubOption({{ $keyOption }})" type="button"
                        class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                        data-target="#delete_modal" data-toggle="tooltip" data-placement="top" title="Subir orden">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <button wire:loading.attr="disabled" wire:click="downSubOption({{ $keyOption }})" type="button"
                        class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                        data-target="#delete_modal" data-toggle="tooltip" data-placement="top" title="Bajar Orden">
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
            <button type="button" wire:click="saveSubQuestion" class="btn btn-sm btn-info" wire:loading.attr="disabled">
                Guardar Sub pregunta
            </button>
        </div>
    </div>
</div>
@endif
@endif
@endif
<div class="row">
    <div class="col-12">
        <table class="table table-striped table-bordered table-hover table-checkable">
            <thead>
                <tr>
                    <th>Acción</th>
                    <th>Etapa</th>
                    <th>Pregunta original</th>
                    <th>Pregunta superior</th>
                    <th>Condición</th>
                    <td>Orden</td>
                    <th class="col-5">Pregunta</th>
                    {{-- <th class="col-5">EN</th> --}}
                    <th>Tipo</th>
                    <th>Obligatoria</th>
                    <th>Indicador</th>
                    <th>Objetivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->survey->surveyQuestionsSub->sortBy('position') as $item)
                <tr>
                    <td nowrap>
                        <button wire:loading.delay.attr="disabled" wire:target="downloadExcel" {{ $this->formEdit ? '' :
                            'disabled' }}
                            wire:click="editQuestion('{{ $item->id }}', true)" type="button"
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
                        <button wire:loading.attr="disabled" wire:click="upSubQuestion('{{ $item->id }}')" type="button"
                            class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                            data-target="#delete_modal" data-toggle="tooltip" data-placement="top" title="Subir orden">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <button wire:loading.attr="disabled" wire:click="downSubQuestion('{{ $item->id }}')"
                            type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="modal"
                            data-target="#delete_modal" data-toggle="tooltip" data-placement="top" title="Bajar Orden">
                            <i class="fas fa-arrow-down"></i>
                        </button>

                        @if($this->isActive($item->id))
                        <button type="button" {{ $this->formEdit ? '' : 'disabled' }}
                            wire:click="activeQuestion('{{ $item->id }}')"
                            class="btn btn-sm btn-clean btn-icon btn-icon-md"
                            title="Pulsa para Desactivar">
                            <i class="fas fa-toggle-on fa-xl" aria-hidden="true"></i>
                        </button>
                        @else
                        <button type="button" {{ $this->formEdit ? '' : 'disabled' }}
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
                        {{ $item->original ? $item->original->question->code : '' }}
                    </td>
                    <td>
                        {{ $item->parent ? $item->parent->question->code : '' }}
                    </td>
                    <td>
                        {{ $item->condition }}
                    </td>
                    <td>
                        {{ $item->order }}
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
            </tbody>
        </table>
    </div>
</div>