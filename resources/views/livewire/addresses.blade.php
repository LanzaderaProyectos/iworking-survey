<div>
    <div class="row">
        <div class="col">
            <div class="kt-portlet__body">
                @livewire(
                    'common.file-upload',
                    [
                        's3' => true,
                        'path' => config('iworking-survey.iworking_public_bucket_folder_survey') . '/' . now()->format('Y/m/d') . '/' . (string) $this->survey->id,
                        'modelId' => (string) $this->survey->id,
                        'model' => 'App\Surveyed',
                        'type' => 'surveyed-excel',
                        'enableUpload' => $this->file ? false : true,
                        'enableDelete' => true,
                        'eventIdentifier' => 'updatedSurveyed',
                    ],
                    key(time() . 'surveyeds-excel')
                )
                <div class="col-10" wire:ignore>
                    <p><b>Destinatario conocido</b></p>
                    <select class="form-control mb-3" id="shippingMails" name="shippingMails"
                        wire:model="shippingMail">
                        <option value="">-- Seleccione una opción --</option>
                        @foreach($this->allSurveyeds as $surveyed)
                            <option value="{{ $surveyed->email }}">{{ $surveyed->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($errorMessage)
                    <div class="alert alert-danger">{{ $errorMessage }}</div>
                    <input type="text" wire:model="errorMessage" hidden>
                @endif
                <div class="col-10 mt-3" wire:ignore>
                    <p><b>Nuevo Destinatario</b></p>
                    <div class="row">
                        <div class="col-md-4 col-3">
                            <div class="form-group">
                                <label for="new_surveyed_name">Nombre</label>
                                <input class="form-control" id="new_surveyed_name" type="text"
                                    wire:model.defer="unregisteredSurveyed.name" placeholder="Nombre de la empresa">
                            </div>
                        </div>
                        <div class="col-md-4 col-3">
                            <div class="form-group">
                                <label for="new_surveyed_nif">NIF</label>
                                <input class="form-control" id="new_surveyed_nif" type="text"
                                    wire:model.defer="unregisteredSurveyed.nif" placeholder="NIF del encuestado">
                            </div>
                        </div>
                        <div class="col-md-4 col-3">
                            <div class="form-group">
                                <label for="new_surveyed_contact_person">Persona de contacto</label>
                                <input class="form-control" id="new_surveyed_contact_person" type="text"
                                    wire:model.defer="unregisteredSurveyed.contactPerson"
                                    placeholder="Nombre del encuestado">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-3">
                            <div class="form-group">
                                <label for="new_surveyed_mail">E-mail</label>
                                <input class="form-control" id="new_surveyed_mail" type="text"
                                    wire:model.defer="unregisteredSurveyed.email" placeholder="Correo del encuestado">
                            </div>
                        </div>
                        <div class="col-md-4 col-3">
                            <div class="form-group">
                                <label for="new_surveyed_language">Idioma</label>
                                <select class="form-control" id="new_surveyed_language" type="text"
                                    wire:model.defer="unregisteredSurveyed.language">
                                    <option value="es">Español</option>
                                    <option value="en">Inglés</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-3">
                            <div class="form-group">
                                <label for="new_surveyed_manager">Responsable</label>
                                <input class="form-control" id="new_surveyed_manager" type="text"
                                    wire:model.defer="unregisteredSurveyed.manager" placeholder="Persona responsable">
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="addNewSurveyed"
                        class="btn btn-sm btn-primary d-flex p-4 py-lg-2 mr-2" wire:loading.attr="disabled">
                        Añadir destinatario
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table id="surveyedsTable" class="table table-striped table-bordered table-hover table-checkable mt-5">
                <thead>
                    <tr>
                        <th>
                            Nombre
                        </th>
                        <td>
                            NIF
                        </td>
                        <th>
                            Persona contacto
                        </th>
                        <th>
                            E-mail
                        </th>
                        <th>
                            Idioma
                        </th>
                        <th>
                            Responsable
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->surveyeds as $user)
                        <tr>
                            <td>
                                {{ $user->name ?? '' }}
                            </td>
                            <td>
                                {{ $user->vat_number ?? '' }}
                            </td>
                            <td>
                                {{ $user->contact_person ?? '' }}
                            </td>
                            <td>
                                {{ $user->email ?? '' }}
                            </td>
                            <td>
                                {{ $user->lang ?? '' }}
                            </td>
                            <td>
                                {{ $user->manager ?? '' }}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-clean btn-icon btn-icon-md" wire:click="removeSurveyed({{ $user }})" 
                                    data-toggle="tooltip" data-placement="top" title="DeleteSurveyed">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>