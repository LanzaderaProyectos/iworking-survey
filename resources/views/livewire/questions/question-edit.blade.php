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
                    <select wire:model.live="typeSelected" class="form-control " id="numbers_format_input"
                        size="2">
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
        </div>
        <div class="row justify-content-end mb-4">
            <div class="col-4">
                <div class="text-right" aria-label="">
                    <button type="button" wire:click="cancel" class="btn btn-sm btn-dark mr-2"
                        wire:loading.attr="disabled">
                        Cancelar
                    </button>
                    <button type="button" wire:click="save" class="btn btn-sm btn-info"
                        wire:loading.attr="disabled">
                        Guardar Pregunta
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
