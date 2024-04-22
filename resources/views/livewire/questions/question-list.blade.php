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
            <div class="table-responsive">
                <table wire:loading.class="table-opacity" wire:target="search"
                    class="table table-striped- table-bordered table-hover table-checkable mt-4" id="users_list_table">
                    <thead scope="col">
                        <tr class="text-uppercase">
                            <th style="width: 20px">Acciones</th>
                            <th style="cursor: pointer">
                                Codigo
                            </th>
                            <th>
                                Nombre  
                            </th>
                            <th style="cursor: pointer">
                                Tipo
                            </th>
                            <th style="cursor: pointer">
                                Tipo de Formulario
                            </th>
                            <th>
                                Fecha creación
                            </th>
                            <th>
                                Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $question)
                            <tr>
                                <td class="text-center">
                                    <a href="{{ route('questions.edit', [
                                        'question' => $question->id,
                                    ]) }}" type="button"
                                        class="btn btn-sm btn-clean btn-icon btn-icon-md" data-toggle="tooltip"
                                        data-placement="top" title="Edit">
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
                                    {{ $questionTypes[$question->survey_type] }}
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
