<div>
    @if (session()->has('surveySended'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span> {{ session('surveySended') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span> {{ session('success') }}</span>
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
                Formularios
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <a href="{{ route('survey.new') }}" class="btn btn-primary btn-sm btn-icon-sm" data-toggle="tooltip"
                        data-placement="top" title="Create">
                        <i class="fas fa-plus"></i>
                        Nuevo formulario
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
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="vatNumber" class="font-weight-bold">Nº Formulario:</label>
                        <input wire:model.live="search.survey_number" type="search" class="form-control form-control-sm"
                            name="survey_number" id="survey_number" placeholder="Nº Formulario">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Nombre:</label>
                        <input wire:model.live="search.name" type="search" class="form-control form-control-sm"
                            name="name-survey" id="name-survey" placeholder="Nombre">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="provider" class="font-weight-bold">Autor:</label>
                        <input wire:model.live="search.author" type="search" class="form-control form-control-sm"
                            name="survey-author" id="survey-author" placeholder="Autor">
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="statusCompany" class="font-weight-bold">Estado:</label>
                        <select wire:model.live="search.status" name="status" id="status"
                            class="form-control form-control-sm">
                            <option value=""> ---- </option>
                            @foreach(MattDaneshvar\Survey\Helpers\Helpers::buildSurveyStatusArray() as $status
                            => $name)
                            <option value="{{ $status }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="statusCompany" class="font-weight-bold">Tipo:</label>
                        <select wire:model.live="search.type" name="status" id="status"
                            class="form-control form-control-sm">
                            <option value=""> ---- </option>
                            @foreach($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label class="font-weight-bold">Mostrar sin proyecto vinculados</label><br>
                        <input type="checkbox" wire:model.live="onlyOriginal" id="originals" name="originals">
                    </div>
                    @if(!$onlyOriginal)
                    <div class="col-6 col-lg-3 col-xl-2 mt-2">
                        <label for="statusCompany" class="font-weight-bold">Proyecto:</label>
                        <select wire:model.live="projectSelected" name="status" id="status"
                            class="form-control form-control-sm">
                            <option value=""> ---- </option>
                            @foreach($projects as $project)
                            <option value="{{ $project['id'] }}">{{ $project['code'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table wire:loading.class="table-opacity" wire:target="search"
                    class="table table-striped- table-bordered table-hover table-checkable mt-4" id="users_list_table">
                    <thead scope="col">
                        <tr class="text-uppercase">
                            <th style="width: 100px">Acciones</th>
                            <th wire:click="sortByTable('survey_number')" style="cursor: pointer">
                                Nº Formulario
                                @include('iworking::partials._sort-icon',['field'=>'survey_number'])
                            </th>
                            @if(!$onlyOriginal)
                            <th>
                                Proyecto
                            </th>
                            @endif
                            <th wire:click="sortByTable('name')">
                                Nombre
                                @include('iworking::partials._sort-icon',['field'=>'name'])
                            </th>
                            <th wire:click="sortByTable('author')" style="cursor: pointer">
                                Autor
                                @include('iworking::partials._sort-icon',['field'=>'author'])
                            </th>
                            <th wire:click="sortByTable('type')" style="cursor: pointer">
                                Tipo
                                @include('iworking::partials._sort-icon',['field'=>'type'])
                            </th>
                            <th wire:click="sortByTable('status')" style="cursor: pointer">
                                Estado
                                @include('iworking::partials._sort-icon',['field'=>'status'])
                            </th>
                            <th>
                                Fecha creación
                            </th>
                            <th>
                                Vencimiento
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surveys as $survey)
                        <tr>
                            <td class="text-center">
                                {{-- @if($draft) --}}

                                @if($survey->status == 0)
                                <a href="{{ route('survey.edit',$survey->id) }}" type="button"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                    data-placement="top" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a onclick="return confirm('¿Estás seguro de borrar este formulario?') || event.stopImmediatePropagation()"
                                    wire:click="delete('{{$survey->id}}')" type="button"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                    data-placement="top" title="Delete"> <i class="fas fa-trash"></i>
                                    @else
                                    <a href="{{ route('survey.show',$survey->id) }}" type="button"
                                        class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                        data-placement="top" title="Edit">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                    {{-- @else
                                    <a href="{{ route('survey.show',$survey->id) }}" type="button"
                                        class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                        data-placement="top" title="Visualizar">
                                        <i class="fas fa-search"></i>
                                    </a>
                                    <a href="{{ route('survey.entry.list',$survey->id) }}" type="button"
                                        class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                        data-placement="top" title="Visualizar entradas">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    @endif --}}
                            </td>
                            <td>
                                {{ $survey->survey_number }}
                            </td>
                            @if(!$onlyOriginal)
                            <td>
                                {{ $this->getProjectSurvey($survey->id) }}
                            </td>
                            @endif
                            <td>
                                {{ $survey->name }}
                            </td>
                            <td>
                                {{ $survey->user->first_name }} {{ $survey->user->last_name }}
                            </td>
                            <td>
                                {{$survey->surveyType->name ?? ''}}
                            </td>
                            <td>
                                @lang('status.Survey.'.$survey->status ?? '')
                            </td>
                            <td>
                                {{ auth()->user()->applyDateFormat($survey->created_at) }}
                            </td>
                            <td>
                                {{ auth()->user()->applyDateFormat($survey->expiration) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="dataTables_info">
                {{ $surveys->links() }}
            </div>
        </div>
    </div>
</div>