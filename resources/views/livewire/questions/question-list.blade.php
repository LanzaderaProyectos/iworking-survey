<div>
    @if (session()->has('surveySended'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span> {{ session('surveySended') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="kt-portlet__body">
        <!--begin: Datatable -->
        <div id="users_list_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
            <div class="dt-buttons btn-group d-flex justify-content-start mb-2">
                <div class="btn-group">
                    <select wire:model="entries" class="custom-select" tabindex="0" aria-controls="users_list_table"
                        type="button" aria-haspopup="true" aria-expanded="false">
                        <option value="10">@lang('iworking::backend.forms.selects.10')</option>
                        <option value="25">@lang('iworking::backend.forms.selects.25')</option>
                        <option value="50">@lang('iworking::backend.forms.selects.50')</option>
                        {{-- <option value="100">@lang('iworking::backend.forms.selects.100')</option> --}}
                    </select>
                </div>
                <div class="btn-group">
                    @if (!$filtersMode)
                    <button wire:click="$toggle('filtersMode')" class="btn rounded-right btn-primary" type="button"
                        data-toggle="collapse" data-target="#collapseFilters" aria-expanded="false"
                        aria-controls="collapseFilters">
                        Mostrar filtros
                    </button>
                    @else
                    <button wire:click="$toggle('filtersMode')" class="btn rounded-right btn-warning" type="button"
                        data-toggle="collapse" data-target="#collapseFilters" aria-expanded="false"
                        aria-controls="collapseFilters">
                        Ocultar filtros
                    </button>
                    @endif
                </div>
                <div class="btn-group ml-3">
                    <button wire:click="downloadExcel" class="btn btn-success rounded-left pl-3 pr-2 " type="button"
                        data-toggle="tooltip" data-placement="top" title="Exportar tabla a Excel">
                        <i class="fas fa-file-excel m-0"></i>
                    </button>
                    <button wire:click="exportToPDF" class="btn btn-danger rounded-right pl-3 pr-2" type="button"
                        data-toggle="tooltip" data-placement="top" title="Exportar tabla a PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <div wire:loading.delay wire:target="exportToPDF, downloadExcel" class="spinner-border ml-2 mt-1"
                        role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span wire:loading.delay wire:target="exportToPDF, downloadExcel" class="align-middle ml-2 pt-2">
                        @lang('iworking::backend.downloading')
                    </span>

                </div>
            </div>
            <div class="collapse" id="collapseFilters" wire:ignore.self>
                <div class="row">
                    <div class="col-6 col-lg-10">
                        <h5>Filtros:</h5>
                    </div>
                    <div class="col-6 col-lg-2">
                        @if (session()->has('surveysearch'))
                        <button wire:click="clearFilters()" class="btn btn-sm btn-danger btn-inline float-right"><i
                                class="fas fa-times"></i> Eliminar filtros</button>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 col-lg-3 mt-2">
                        <label for="vatNumber" class="font-weight-bold">Codigo:</label>
                        <input wire:model.live.debounce.300ms="filters.code" type="search"
                            class="form-control form-control-sm" name="filters.code" id="filters.code"
                            placeholder="Codigo">
                    </div>
                    <div class="col-6 col-lg-3 mt-2">
                        <label for="provider" class="font-weight-bold">Nombre:</label>
                        <input wire:model.live.debounce.300ms="filters.name" type="search"
                            class="form-control form-control-sm" name="filters.name" id="filters.name"
                            placeholder="Nombre">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Tipo:</label>
                        <select class="form-control" wire:model.live.debounce.300mss="filters.type">
                            <option value="">Seleccióna una opción</option>
                            @foreach($typeAnwers as $keyType=>$type)
                            <option vlaue="{{$keyType}}">{{$type}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Tipo de formulario:</label>
                        <select class="form-control" wire:model.live.debounce.300ms="filters.form_type">
                            <option value="">Seleccióna una opción</option>
                            @foreach($questionTypes as $keyType=>$type)
                            <option vlaue="{{$keyType}}">{{$type}}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Fecha creación desde:</label>
                        <input wire:model.live.debounce.300ms="filters.created_from" type="date"
                            class="form-control form-control-sm" name="filters.created_from" id="filters.created_from">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Fecha creación hasta:</label>
                        <input wire:model.live.debounce.300ms="filters.created_to" type="date"
                            class="form-control form-control-sm" name="filters.created_to" id="filters.created_to">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <p><label for="provider" class="font-weight-bold">Desactivadas</label></p>
                        <input wire:model.live="filters.disabled" name="toggle_rgpd" type="checkbox" class="mt-n3">
                        <span style="margin-left: 5px"></span>
                    </div>
                    @if($this->filters['disabled'])
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Desactivada desde:</label>
                        <input wire:model.live.debounce.300ms="filters.disabled_from" type="date"
                            class="form-control form-control-sm" name="filters.disabled_from" id="filters.disabled_from">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Desactivada hasta:</label>
                        <input wire:model.live.debounce.300ms="filters.disabled_to" type="date"
                            class="form-control form-control-sm" name="filters.disabled_to" id="filters.disabled_to">
                    </div>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table wire:loading.class="table-opacity" wire:target="filters"
                    class="table table-striped- table-bordered table-hover table-checkable mt-4" id="users_list_table">
                    <thead scope="col">
                        <tr class="text-uppercase">
                            <th style="width: 3%">Acciones</th>
                            <th wire:click="sortByTable('code')" style="cursor: pointer; width: 10%;">
                                Codigo
                                @include('iworking::partials._sort-icon',['field'=>'code'])
                            </th>
                            <th wire:click="sortByTable('content')" style="cursor: pointer; width: 25%;">
                                Nombre
                                @include('iworking::partials._sort-icon',['field'=>'content'])
                            </th>
                            <th wire:click="sortByTable('type')" style="cursor: pointer; width: 13%;">
                                Tipo
                                @include('iworking::partials._sort-icon',['field'=>'type'])
                            </th>
                            <th wire:click="sortByTable('survey_type')" style="cursor: pointer; width: 13%;">
                                Tipo de Formulario
                                @include('iworking::partials._sort-icon',['field'=>'survey_type'])
                            </th>
                            <th wire:click="sortByTable('created_at')" style="cursor: pointer; width: 13%;">
                                Fecha creación
                                @include('iworking::partials._sort-icon',['field'=>'created_at'])
                            </th>
                            <th wire:click="sortByTable('disabled')" style="cursor: pointer; width: 10%;">
                                Estado
                                @include('iworking::partials._sort-icon',['field'=>'disabled'])
                            </th>
                            <th wire:click="sortByTable('disabled_at')" style="cursor: pointer; width: 13%;">
                                Fecha desactivación
                                @include('iworking::partials._sort-icon',['field'=>'disabled_at'])
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $question)
                        <tr>
                            <td class="text-center">
                                <a href="{{ route('questions.edit', [
                                        'question' => $question->id,
                                    ]) }}" type="button" class="btn btn-sm btn-clean btn-icon btn-icon-md"
                                    data-toggle="tooltip" data-placement="top" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                            <td>
                                {{ $question->code }}
                            </td>
                            <td>
                                {{ $question->getTranslation('content', 'es') }}

                            </td>
                            <td>
                                {{ $typeAnwers[$question->type] }}
                            </td>
                            <td>
                                {{ $question->survey_type }}
                            </td>
                            <td>
                                {{ auth()->user()->applyDateFormat($question->created_at) }}
                            </td>
                            <td>
                                @if($question->disabled)
                                Desactivada
                                @else
                                Activa
                                @endif
                            </td>
                            <td>
                                @if($question->disabled)
                                {{ auth()->user()->applyDateFormat($question->disabled_at) }}
                                @else
                                -
                                @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="dataTables_info">
                {{ $questions->links() }}
            </div>
        </div>
    </div>
</div>