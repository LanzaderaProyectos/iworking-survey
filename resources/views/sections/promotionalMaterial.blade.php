<div id="accordion">
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex align-items-center"
                    style="gap: 15px; text-decoration: none !important;" data-toggle="collapse"
                    data-target="#collapseSectionProfesional" aria-expanded="true"
                    aria-controls="collapseSectionProfesional">
                    <span class="h3">Material Promocional</span>
                    <i class="fas fa-chevron-up tab-arrow"></i>
                </button>
            </h5>
        </div>
        <div id="collapseSectionProfesional" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion"
            wire:ignore.self>
            <div class=" offset-6 col-6 offset-md-8 col-md-4 offset-xl-10 col-xl-2 mt-3">
                <button wire:loading.attr="disabled" wire:click="exportOrderToExcel"
                    class="btn btn-success rounded-left pl-3 pr-2" type="button" data-toggle="tooltip"
                    data-placement="top" title="Exportar tabla Excel">
                    <i class="fas fa-file-excel m-0" aria-hidden="true"></i>
                </button>
                <button wire:click="exportOrderToPDF" class="btn btn-danger rounded-right pl-3 pr-2" type="button"
                    data-toggle="tooltip" data-placement="top" title="Exportar tabla a PDF">
                    <i class="fas fa-file-pdf"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="p-4 border-bottom">
                    <table class="table table-striped- table-bordered table-hover table-checkable mt-4"
                        id="comandTable">
                        <thead>
                            <tr>
                                <th style="width: 10%;">Acción</th>
                                <th style="width: 70%;">Material Promocional</th>
                                <th style="width: 20%;">Unidades</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <button class="btn btn-primary">+</button>
                                </td>
                                <td>
                                    <select {{ ($disabled ?? false) ? 'disabled' : '' }} class="form-control">
                                        <option value="">Seleccione material</option>
                                        
                                    </select>
                                </td>
                                <td>
                                    <input {{ ($disabled ?? false) ? 'disabled' : '' }} type="number"
                                        class="form-control">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>