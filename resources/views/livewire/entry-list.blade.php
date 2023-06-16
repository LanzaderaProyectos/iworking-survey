<div>
    @if (session()->has('reminderMails'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span> {!! session('reminderMails') !!}</span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="dt-buttons btn-group">
        <div class="btn-group">
            <select wire:model="entries" class="custom-select" tabindex="0" aria-controls="users_list_table"
                type="button" aria-haspopup="true" aria-expanded="false">
                <option value="10">Mostrar 10 filas</option>
                <option value="25">Mostrar 25 filas</option>
                <option value="50">Mostrar 50 filas</option>
                <option value="100">Mostrar 100 filas</option>
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
            <button wire:loading.attr="disabled" wire:click="downloadExcel"
                class="btn btn-success rounded-left pl-3 pr-2" type="button" data-toggle="tooltip" data-placement="top"
                title="Exportar tabla Excel">
                <i class="fas fa-file-excel m-0" aria-hidden="true"></i>
            </button>
            <button wire:click="exportToPDF" class="btn btn-danger rounded-right pl-3 pr-2" type="button"
                data-toggle="tooltip" data-placement="top" title="Exportar tabla a PDF">
                <i class="fas fa-file-pdf"></i>
            </button>
            <div wire:loading.delay="" wire:target="downloadExcel" class="spinner-border ml-2 mt-1"
                role="status">
                <span class="sr-only"></span>
            </div>
            <span wire:loading.delay="" wire:target="downloadExcel" class="aling-middle ml-2 pt-2">
                Descargando... </span>
        </div>
        <div class="btn-group ml-4">
            <button wire:loading.attr="disabled"
                onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
                wire:click="sendReminder" class="btn btn-warning rounded" type="button" data-toggle="tooltip"
                data-placement="top" title="Enviar recordatorio">
                Enviar recordatorio
            </button>
        </div>
    </div>
    <div class="collapse" id="collapseFilters" wire:ignore.self>
        <div class="row mt-4">
            <div class="col-6 col-lg-10">
                <h5>Filtros:</h5>
            </div>
            {{-- <div class="col-6 col-lg-2">
                @if (session()->has('surveysearch'))
                <button wire:click="clearFilters()" class="btn btn-sm btn-danger btn-inline float-right"><i
                        class="fas fa-times"></i> Eliminar filtros</button>
                @endif
            </div> --}}
        </div>
        <div class="row mb-3">
            <div class="col-md-4 col-6">
                <label for="vatNumber" class="font-weight-bold">Encuestado:</label>
                <input wire:model.debounce.300ms="search.surveyed" type="search" class="form-control form-control-sm"
                    name="survey_number" id="survey_number" placeholder="Nº Encuesta">
            </div>
            <div class="col-md-4 col-6">
                <label for="provider" class="font-weight-bold">Responsable:</label>
                <input wire:model.debounce.300ms="search.manager" type="search" class="form-control form-control-sm"
                    name="name-survey" id="name-survey" placeholder="Nombre">
            </div>
            <div class="col-md-4 col-6">
                <label for="statusCompany" class="font-weight-bold">Estado:</label>
                <select wire:model.debounce.300ms="search.status" name="status" id="status"
                    class="form-control form-control-sm">
                    <option value=""> ---- </option>
                    @foreach (MattDaneshvar\Survey\Helpers\Helpers::buildEntryStatusArray() as $status => $name)
                        <option value="{{ $status }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-6 mt-3">
                <label for="provider" class="font-weight-bold">Puntuación:</label>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="dateFrom">Mínimo</label>
                        <input wire:model.debounce.300ms="search.min" type="text"
                            class="form-control form-control-sm" placeholder="Desde">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="dateTo">Máximo</label>
                        <input wire:model.debounce.300ms="search.max" type="text"
                            class="form-control form-control-sm" placeholder="Hasta">
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="row py-3">
        <div class="col-md-4 col-12">
            <div class="has-search">
                <span class="fa fa-search form-control-feedback pt-1" aria-hidden="true"></span>
                <input wire:loading.attr="disabled" wire:target="downloadExcel" wire:model="search" type="search"
                    class="form-control" placeholder="Buscar..." title="Buscar por palabra clave">
            </div>
        </div>
    </div> --}}
    <div class="row mt-4">
        <div class="col">
            <table class="table table-striped- table-bordered table-hover table-checkable">
                <thead>
                    <tr class="text-uppercase">
                        <th style="width: 20px">Acciones</th>
                        <th>Encuestado</th>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Persona contacto</th>
                        <th>Idioma</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th>Puntuación
                            <br>
                            <span class="bg-warning rounded p-1">
                                (Max. {{ $this->totalPoints }})
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($surveyEntries as $entry)
                        <tr>
                            <td class="text-center">
                                <a href="{{ route('survey.entry', $entry->id) }}" type="button"
                                    class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                    data-placement="top" title="Visualizar">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                            <td>
                                {{ $entry->surveyed->name ?? '' }}
                            </td>
                            <td>
                                {{ $entry->surveyed->vat_number ?? '' }}
                            </td>
                            <td>{{ $entry->participant }}
                            </td>
                            <td>
                                {{ $entry->surveyed->contact_person ?? '' }}
                            </td>
                            <td>
                                {{ $entry->lang ?? '' }}
                            </td>
                            <td>
                                {{ $entry->surveyed->manager ?? '' }}
                            </td>
                            <td>
                                @lang('survey::status.entry.' . $entry->status ?? '')
                            </td>
                            <td>
                                {{ $entry->sum_score }} - {{ $this->totalPoints }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
