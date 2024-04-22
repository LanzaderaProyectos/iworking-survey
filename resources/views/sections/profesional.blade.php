<div id="accordion">
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex align-items-center"
                    style="gap: 15px; text-decoration: none !important;" data-toggle="collapse"
                    data-target="#collapseSectionProfesional" aria-expanded="true"
                    aria-controls="collapseSectionProfesional">
                    <span class="h3">Profesional</span>
                    <i class="fas fa-chevron-up tab-arrow"></i>
                </button>
            </h5>
        </div>
        <div id="collapseSectionProfesional" class="collapse show" aria-labelledby="headingTwo"
            data-parent="#accordion" wire:ignore.self>

            <div class="card-body">

                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> Selecciona el profesional*</label>:
                    </div>
                    <select class="form-control" wire:model.live="selectedProfessionalId">
                        <option value="">Selecciona un profecional</option>
                        @foreach($this->professionalsSurvey as $profesional)
                        <option value="{{ $profesional->id }}">{{ $profesional->first_name }} {{
                            $profesional->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if(!empty($this->selectedProfessional))
            <div class="card-body">
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 1- Tratamiento*</label>:
                    </div>
                    <select class="form-control" wire:model="selectedProfessional.treatment_id" {{($disabled ??
                        false) ? 'disabled' : '' }}>
                        @foreach($professionalSelectOptions["treatments"] as $treatment)
                        <option value="{{ $treatment->id }}">{{ $treatment->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 2- Nombre del profesional*</label>:
                    </div>
                    <input type="text" wire:model="selectedProfessional.first_name"
                        name="Nombre del profesional" class="form-control" {{($disabled ?? false) ? 'disabled'
                        : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 3- Apellido del profesional*</label>:
                    </div>
                    <input type="text" wire:model="selectedProfessional.last_name" name="Nombre del profesional"
                        class="form-control" {{($disabled ?? false) ? 'disabled' : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 4- NIF del profesional*</label>:
                    </div>
                    <input type="text" wire:model="selectedProfessional.nif" name="Nombre del profesional"
                        class="form-control" {{($disabled ?? false) ? 'disabled' : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 5- Funcción a cargo*</label>:
                    </div>
                    <select class="form-control" wire:model="selectedProfessional.job_title_id" {{($disabled ??
                        false) ? 'disabled' : '' }}>
                        @foreach($professionalSelectOptions["jobTitles"] ?? [] as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 6- Telefono del Profesional*</label>:
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select wire:model="selectedProfessional.prefix_phone" class="form-control"
                                {{($disabled ?? false) ? 'disabled' : '' }}>
                                <option value="">Selecciona</option>
                                @foreach (\App\Library\Constants::PHONE_PREFIX as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-9">
                            <div class="tab-pane fade show active" role="tabpanel"
                                aria-labelledby="nav-survey-es-tab">
                                <input wire:model="selectedProfessional.phone" type="number"
                                    x-mask:dynamic="'999999999'" name="phone"
                                    class="form-control form-control-alternative"
                                    placeholder="Teléfono contacto" {{($disabled ?? false) ? 'disabled' : '' }}>
                                @error('personalInformation.phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                                @error('personalInformation.prefix_phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 7- Movil del Profesional*</label>:
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select wire:model="selectedProfessional.prefix_mobile" class="form-control"
                                {{($disabled ?? false) ? 'disabled' : '' }}>
                                <option value="">Selecciona</option>
                                @foreach (\App\Library\Constants::PHONE_PREFIX as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-9">
                            <div class="tab-pane fade show active" role="tabpanel"
                                aria-labelledby="nav-survey-es-tab">
                                <input wire:model="selectedProfessional.mobile_phone" type="number"
                                    x-mask:dynamic="'999999999'" name="phone"
                                    class="form-control form-control-alternative" placeholder="Movil contacto"
                                    {{($disabled ?? false) ? 'disabled' : '' }}>
                                @error('personalInformation.phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                                @error('personalInformation.prefix_phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 8- Correo electrónico del
                            profesional*</label>:
                    </div>
                    <input wire:model="selectedProfessional.mail_contact" type="email"
                        name="Correo electrónico del profesional" class="form-control" {{($disabled ?? false)
                        ? 'disabled' : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 9-Nombre del centro visitado *</label>:
                    </div>
                    <input type="text" name="Nombre del centro visitado" class="form-control" {{($disabled ??
                        false) ? 'disabled' : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 10-Razón Social del centro *</label>:
                    </div>
                    <input type="text" name="Razón Social del centro" class="form-control" {{($disabled ??
                        false) ? 'disabled' : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 11- Teléfono del centro*</label>:
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select class="form-control" {{($disabled ?? false) ? 'disabled' : '' }}>
                                <option value="">Selecciona</option>
                                @foreach (\App\Library\Constants::PHONE_PREFIX as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-9">
                            <div class="tab-pane fade show active" role="tabpanel"
                                aria-labelledby="nav-survey-es-tab">
                                <input type="number" x-mask:dynamic="'999999999'" name="phone"
                                    class="form-control form-control-alternative"
                                    placeholder="Teléfono contacto centro" {{($disabled ?? false) ? 'disabled'
                                    : '' }}>
                                @error('personalInformation.phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                                @error('personalInformation.prefix_phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 12- Teléfono 2 del centro*</label>:
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select class="form-control" {{($disabled ?? false) ? 'disabled' : '' }}>
                                <option value="">Selecciona</option>
                                @foreach (\App\Library\Constants::PHONE_PREFIX as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-9">
                            <div class="tab-pane fade show active" role="tabpanel"
                                aria-labelledby="nav-survey-es-tab">
                                <input type="number" x-mask:dynamic="'999999999'" name="phone"
                                    class="form-control form-control-alternative"
                                    placeholder="Teléfono contacto centro 2" {{($disabled ?? false) ? 'disabled'
                                    : '' }}>
                                @error('personalInformation.phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                                @error('personalInformation.prefix_phone')
                                <span class="text-danger text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 13- Correo electrónico del centro*</label>:
                    </div>
                    <input type="email" name="Correo electrónico del profesional" class="form-control"
                        {{($disabled ?? false) ? 'disabled' : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 14- Dirección del centro*</label>:
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <select class="form-control" {{($disabled ?? false) ? 'disabled' : '' }}>
                                <option value="">Selecciona</option>
                                <option value="Calle">Calle</option>
                                <option value="Av">Avenida</option>
                                <option value="Bulevar">Bulevar</option>
                                <option value="Rambla">Rambla</option>
                            </select>
                        </div>
                        <div class="col-9">
                            <div class="tab-pane fade show active" role="tabpanel"
                                aria-labelledby="nav-survey-es-tab">
                                <input type="text" class="form-control" placeholder="Dirección del centro"
                                    {{($disabled ?? false) ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 15- Categoria para este proyecto*</label>:
                    </div>
                    <select class="form-control" {{($disabled ?? false) ? 'disabled' : '' }}>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="sc">SC</option>
                    </select>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 16- Otros detalles del
                            profesional*</label>:
                    </div>
                    <input type="text" wire:model="selectedProfessional.other_contact_information"
                        name="Otros detalles del profesional" class="form-control" {{($disabled ?? false)
                        ? 'disabled' : '' }}>
                </div>
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 17- Solicitado consentimiento*</label>:
                    </div>
                    <input type="checkbox" wire:model="selectedProfessional.consent_request" {{($disabled ??
                        false) ? 'disabled' : '' }}>
                </div>
                @if($selectedProfessional->consent_request)
                <div class="p-4 border-bottom">
                    <div class="form-group mb-0">
                        <label style="font-size:1.1rem"> 18- Consentimiento*</label>:
                    </div>
                    <select wire:model="selectedProfessional.consent" class="form-control" {{($disabled ??
                        false) ? 'disabled' : '' }}>
                        <option value="yes">Si</option>
                        <option value="no">No</option>
                    </select>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>