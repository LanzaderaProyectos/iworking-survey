<div>
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
            <div wire:loading.delay="" wire:target="downloadExcel" class="spinner-border ml-2 mt-1" role="status">
                <span class="sr-only"></span>
            </div>
            <span wire:loading.delay="" wire:target="downloadExcel" class="aling-middle ml-2 pt-2">
                Descargando... </span>
        </div>
    </div>
    <div class="row py-3">
        <div class="col-md-4 col-12">
            <div class="has-search">
                <span class="fa fa-search form-control-feedback pt-1" aria-hidden="true"></span>
                <input wire:loading.attr="disabled" wire:target="downloadExcel" wire:model="search" type="search"
                    class="form-control" placeholder="Buscar..." title="Buscar por palabra clave">
            </div>
        </div>
    </div>

    <div class="row">
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
                        <th>Puntuación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveyEntries as $entry)
                    <tr>
                        <td class="text-center">
                            <a href="{{ route('survey.entry',$entry->id) }}" type="button"
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
                        <td>{{
                            $entry->participant }}
                        </td>
                        <td>
                            {{ $entry->surveyed->contact_person ?? ''}}
                        </td>
                        <td>
                            {{ $entry->lang ?? '' }}
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
        </div>
    </div>
</div>