<div>
    @if (session()->has('questionSaved'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span> {{ session('questionSaved') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="kt-portlet__body">
        <div class="row">
            <div class="col-12">
                <h5>
                    Preguntas
                </h5>
            </div>
            <div class="col-md-12 mt-3">
                <div class="form-group">
                    <label class="form-control-label" for="input-first_name">Nombre*</label>
                    <nav id="create-questions">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-question-tab" data-toggle="tab"
                                href="#nav-question-es" role="tab" aria-controls="nav-home"
                                aria-selected="true">Español</a>
                            <a class="nav-item nav-link" id="nav-question-tab" data-toggle="tab" href="#nav-question-en"
                                role="tab" aria-controls="nav-profile" aria-selected="false">Ingles</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-question-es" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <input wire:model.defer="questionName.es" type="text" name="section-name"
                                id="question-name-es" class="form-control form-control-alternative"
                                placeholder="Introduzca nombre">
                        </div>
                        <div class="tab-pane fade" id="nav-question-en" role="tabpanel"
                            aria-labelledby="nav-profile-tab">
                            <input wire:model.defer="questionName.en" type="text" name="section-name"
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
                <div class="form-group mb-3">
                    <label for="numbers_format">Tipo*:</label>
                    <select wire:model.live="typeSelected" class="form-control " id="numbers_format_input" size="4">
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
            <div class="col-md-5 col-12">
                <div class="form-group mb-3">
                    <label for="numbers_format">Para que tipo de formulario*:</label>
                    <select wire:model.live="surveyType" class="form-control " id="numbers_format_input" size="4">
                        @foreach ($questionTypes as $key => $value)
                        <option value="{{ $key }}">
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                    @error('questionSelected')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        @if($customOptions)
        <div class="row mt-n3">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-control-label" for="input-first_name">Añadir Opción</label>
                    <nav id="create-option">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-option-tab" data-toggle="tab"
                                href="#nav-option-es" role="tab" aria-controls="nav-home"
                                aria-selected="true">Español</a>
                            <a class="nav-item nav-link" id="nav-option-tab" data-toggle="tab" href="#nav-option-en"
                                role="tab" aria-controls="nav-profile" aria-selected="false">Ingles</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-option-es" role="tabpanel"
                            aria-labelledby="nav-home-tab">
                            <div class="d-flex">
                                <input wire:model.defer="newOptionES" type="text" name="section-name"
                                    id="option-name-es" class="form-control form-control-alternative"
                                    placeholder="Introduzca opción">
                                @if($updateOption != null)
                                <button wire:click="addOption" class="btn btn-dark" type="button" title="Guardar">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                @else
                                <button wire:click="addOption" class="btn btn-dark" type="button"
                                    title="Añadir">+</button>
                                @endif
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-option-en" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="d-flex">
                                <input wire:model.defer="newOptionEN" type="text" name="section-name"
                                    id="option-name-en" class="form-control form-control-alternative"
                                    placeholder="Introduzca opción">
                                @if($updateOption != null)
                                <button wire:click="addOption" class="btn btn-dark" type="button" title="Guardar">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                @else
                                <button wire:click="addOption" class="btn btn-dark" type="button"
                                    title="Añadir">+</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @error('newOptionEN')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @error('newOptionES')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="row  mb-3">
            <div class="col-md-12 mt-3">
                <table class="table table-striped table-bordered table-hover table-checkable">
                    <thead>
                        <tr>
                            <th style="width: 10%">Acción</th>
                            <th style="width: 40%">ES</th>
                            <th style="width: 40%">EN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($optionES as $keyOption => $option)
                        <tr>
                            <td><button wire:loading.delay.attr="disabled" wire:target="downloadExcel"
                                    wire:click="editOption('{{ $keyOption }}')" type="button"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                    data-placement="top" title="Edit">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </button>
                                <button type="button" onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') ||
                            event.stopImmediatePropagation();" wire:click="deleteOption('{{ $keyOption }}')"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Delete">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td>{{ $option }}</td>
                            <td>{{ $optionEN[$keyOption] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        <div class="row justify-content-end mb-4">
            <div class="col-4">
                <div class="text-right" aria-label="">
                    <button type="button" wire:click="cancel" class="btn btn-sm btn-dark mr-2"
                        wire:loading.attr="disabled">
                        Cancelar
                    </button>
                    <button type="button" wire:click="save" class="btn btn-sm btn-info" wire:loading.attr="disabled">
                        Guardar Pregunta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>