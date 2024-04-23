<div id="accordion">
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex align-items-center"
                    style="gap: 15px; text-decoration: none !important;" data-toggle="collapse"
                    data-target="#collapseSectionProfesional" aria-expanded="true"
                    aria-controls="collapseSectionProfesional">
                    <span class="h3">Comanda</span>
                    <i class="fas fa-chevron-up tab-arrow"></i>
                </button>
            </h5>
        </div>
        <div id="collapseSectionProfesional" class="collapse show" aria-labelledby="headingTwo"
            data-parent="#accordion" wire:ignore.self>

            <div class="card-body">

                <div class="p-4 border-bottom">
                    <table class="table table-striped- table-bordered table-hover table-checkable mt-4" id="comandTable">
                        <thead>
                            <tr>
                                <th>Tipo de pedido</th>
                                <th>Producto</th>
                                <th>Unidades</th>
                                <th>Facturación sin iva</th>
                                <th>Motivos no interesado</th>
                                <th>Comentarios</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select  {{ ($disabled ?? false) ? 'disabled' : '' }} class="form-control">
                                        <option value="">Seleccione una opción</option>
                                        <option value="1">Preventa</option>
                                        <option value="2">Campaña</option>
                                        <option value="3">Pedido normal</option>
                                    </select>
                                </td>
                                <td>
                                    Producto correspondiente
                                </td>
                                <td>
                                    <input  {{ ($disabled ?? false) ? 'disabled' : '' }} type="number" class="form-control">
                                </td>
                                <td>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" {{ ($disabled ?? false) ? 'disabled' : '' }}>
                                        <div class="input-group-append">
                                            <span class="input-group-text">€</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control" {{ ($disabled ?? false) ? 'disabled' : '' }}>
                                </td>
                                <td>
                                    <textarea class="form-control" {{ ($disabled ?? false) ? 'disabled' : '' }} style="resize: vertical" rows="2"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>