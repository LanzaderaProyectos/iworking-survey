<div>
    @if (session()->has('surveySended'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span> {{ session('surveySended') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand flaticon2-line-chart"></i>
            </span>
            <h3 class="kt-portlet__head-title text-uppercase">
                Encuestas
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <a href="{{ route('survey.new') }}" class="btn btn-primary btn-sm btn-icon-sm" data-toggle="tooltip"
                        data-placement="top" title="Create">
                        <i class="fas fa-plus"></i>
                        Nueva encuesta
                    </a>
                </div>
            </div>
        </div>
    </div>
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
                        {{-- <option value="100">@lang('iworking::backend.forms.selects.100')</option>--}}
                    </select>
                </div>
                <div class="btn-group">
                    {{-- @if (!$filtersMode)
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
                    @endif --}}
                </div>
                <div class="btn-group ml-3">
                    {{-- <button wire:click="downloadExcel" class="btn btn-success rounded-left pl-3 pr-2 "
                        type="button" data-toggle="tooltip" data-placement="top" title="Exportar tabla a Excel">
                        <i class="fas fa-file-excel m-0"></i>
                    </button>
                    <button wire:click="exportToPDF" class="btn btn-danger rounded-right pl-3 pr-2" type="button"
                        data-toggle="tooltip" data-placement="top" title="Exportar tabla a PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button> --}}
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
                        @if (session()->has('ordersearch'))
                        <button wire:click="clearFilters()" class="btn btn-sm btn-danger btn-inline float-right"><i
                                class="fas fa-times"></i> Eliminar filtros</button>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="vatNumber" class="font-weight-bold">NIF/VAT:</label>
                        <input wire:model.debounce.300ms="search.vatNumber" type="search"
                            class="form-control form-control-sm" name="vatNumber" id="vatNumber" placeholder="NIF/VAT">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Proveedor:</label>
                        <input wire:model.debounce.300ms="search.provider" type="search"
                            class="form-control form-control-sm" name="provider" id="provider" placeholder="Proveedor">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Aprobador:</label>
                        <input wire:model.debounce.300ms="search.manager" type="search"
                            class="form-control form-control-sm" name="manager" id="manager" placeholder="Aprobador">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="statusCompany" class="font-weight-bold">Estado:</label>
                        <select wire:model.debounce.300ms="search.status" name="status" id="status"
                            class="form-control form-control-sm">
                            <option value=""> ---- </option>
                            @foreach(\Iworking\IworkingBoilerplate\Helpers\Helpers::buildOrdersStatusArray() as $status
                            => $name)
                            <option value="{{ $status }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(!$draft)
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="cost_center_id" class="font-weight-bold">Autor:</label>
                        <select wire:model.debounce.300ms="search.autor" name="autor" id="autor"
                            class="form-control form-control-sm">
                            <option value=""> ---- </option>
                            {{-- @foreach($autors as $autor)
                            <option value="{{ $autor->id }}">{{ $autor->first_name }} {{ $autor->last_name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                    @endif
                </div>
                <div class="row mb-3">
                    <div class="col-8 col-lg-6 col-xl-6 mt-2">
                        <label for="createdAt" class="font-weight-bold">Fecha de creación:</label>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="dateFrom">Desde</label>
                                <input wire:model.debounce.300ms="search.createdAtFrom" type="date"
                                    class="form-control form-control-sm" name="createdAtFrom" id="createdAtFrom"
                                    placeholder="Desde">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="dateTo">Hasta</label>
                                <input wire:model.debounce.300ms="search.createdAtTo" type="date"
                                    class="form-control form-control-sm" name="createdAtTo" id="createdAtTo"
                                    placeholder="Hasta">
                            </div>
                        </div>
                    </div>
                    <div class="col-8 col-lg-6 col-xl-6 mt-2">
                        <label for="date" class="font-weight-bold">Fecha de entrega:</label>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="dateFrom">Desde</label>
                                <input wire:model.debounce.300ms="search.dateFrom" type="date"
                                    class="form-control form-control-sm" name="dateFrom" id="dateFrom"
                                    placeholder="Desde">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="dateTo">Hasta</label>
                                <input wire:model.debounce.300ms="search.dateTo" type="date"
                                    class="form-control form-control-sm" name="dateTo" id="dateTo" placeholder="Hasta">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table wire:loading.class="table-opacity" wire:target="search"
                    class="table table-striped- table-bordered table-hover table-checkable mt-4" id="users_list_table">
                    <thead scope="col">
                        <tr class="text-uppercase">
                            <th></th>
                            <th wire:click="sortBy('survey_number')" style="cursor: pointer">
                                Nº Encuesta
                                @include('iworking::partials._sort-icon',['field'=>'survey_number'])
                            </th>
                            <th wire:click="sortBy('name')">
                                Nombre
                                @include('iworking::partials._sort-icon',['field'=>'name'])
                            </th>
                            <th wire:click="sortBy('author')" style="cursor: pointer">
                                Autor
                                @include('iworking::partials._sort-icon',['field'=>'author'])
                            </th>
                            <th wire:click="sortBy('status')" style="cursor: pointer">
                                Estado
                                @include('iworking::partials._sort-icon',['field'=>'status'])
                            </th>
                            <th>
                                Fecha creación
                            </th>
                            <th>
                                Vencimiento
                            </th>
                            <th>
                                Puntuación media
                            </th>
                            <th style="width: 20px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surveys as $survey)
                        <tr>
                            <td>
                                <a class="" href="#collapseRow-{{ $survey->id }}" data-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="collapseRow">
                                    <i class="fa fa-caret-right"></i>
                                </a>
                            </td>
                            <td>
                                {{ $survey->survey_number }}
                            </td>
                            <td>
                                {{ $survey->name }}
                            </td>
                            <td>
                                {{ $survey->user->first_name }} {{ $survey->user->last_name }}
                            </td>
                            <td>
                                @lang('survey::status.survey.'.$survey->status ?? '')
                            </td>
                            <td>
                                {{ auth()->user()->applyDateFormat($survey->created_at) }}
                            </td>
                            <td>
                                {{ auth()->user()->applyDateFormat($survey->expiration) }}
                            </td>
                            <td>
                            </td>
                            <td class="text-center">
                                @if($draft)
                                <a href="{{ route('survey.edit',$survey->id) }}" type="button"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                    data-placement="top" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @else
                                <a href="{{ route('survey.show',$survey->id) }}" type="button"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                    data-placement="top" title="Visualizar">
                                    <i class="fas fa-search"></i>
                                </a>
                                @endif
                                </span>
                            </td>
                        </tr>
                        <tr class="collapse" id="collapseRow-{{ $survey->id }}" wire:ignore.self>
                            <td></td>
                            <td colspan="10" class="px-1">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th>Encuestado</th>
                                            <th>ID</th>
                                            <th>Email</th>
                                            <th>Persona contacto</th>
                                            <th>Idioma</th>
                                            <th>Responsable</th>
                                            <th>Estado</th>
                                            <th>Puntuación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($survey->entries as $entry)
                                        <tr>
                                            <td>
                                                {{ $entry->surveyed->name ?? '' }}
                                            </td>
                                            <td>
                                                {{ $entry->surveyed->vat_number ?? '' }}
                                            </td>
                                            <td>
                                                <a href="{{ route('survey.entry', ['entryId' => $entry->id])}}">{{
                                                    $entry->participant }}</a>
                                            </td>
                                            <td>
                                                {{ $entry->surveyed->contact_person ?? ''}}
                                            </td>
                                            <td>
                                                {{ $entry->lang }}
                                            </td>
                                            <td>
                                                {{ $entry->surveyed->manager ?? '' }}
                                            </td>
                                            <td>
                                                @lang('survey::status.entry.'.$entry->status ?? '')
                                            </td>
                                            <td>
                                                {{$entry->answers->sum('score')}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="dataTables_info">
                {{-- {{ $surveys->links() }} --}}
            </div>
        </div>
    </div>
</div>