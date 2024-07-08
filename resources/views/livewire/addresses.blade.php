<div>
    <div class="row">
        <div class="col">
            <div class="kt-portlet__body">
                @livewire('common.file-upload',[
                's3' => true,
                'path' => config('iworking-survey.iworking_public_bucket_folder_survey') . '/' . now()->format('Y/m/d')
                .
                '/'
                . (string)$this->survey->id,
                'modelId' => (string)$this->survey->id,
                'model' => 'App\Surveyed',
                'type' => 'surveyed-excel',
                'enableUpload' => $this->file ? false : true,
                'enableDelete' => true,
                'eventIdentifier' => 'updatedSurveyed'
                ], key(time() . 'surveyeds-excel'))
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table table-striped table-bordered table-hover table-checkable mt-5">
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>