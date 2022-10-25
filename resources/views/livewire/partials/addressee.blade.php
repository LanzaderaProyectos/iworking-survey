<div class="row">
    <div class="col">
        <table class="table table-striped table-bordered table-hover table-checkable mt-5">
            <thead>
                <tr>
                    <th>
                        Codigo
                    </th>
                    <th>
                        Nombre
                    </th>
                    <td>
                        NIF
                    </td>
                    <th>
                        Tipo
                    </th>
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
                @foreach ($this->users as $user)
                <tr>
                    <td>
                        {{ $user->codigo ?? '' }}
                    </td>
                    <td>
                        {{ $user->nombre ?? '' }}
                    </td>
                    <td>
                        {{ $user->nif ?? '' }}
                    </td>
                    <td>
                        {{ $user->tipo ?? '' }}
                    </td>
                    <td>
                        {{ $user->contacto ?? '' }}
                    </td>
                    <td>
                        {{ $user->email ?? '' }}
                    </td>
                    <td>
                        {{ $user->idioma ?? '' }}
                    </td>
                    <td>
                        {{ $user->responsable ?? '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>