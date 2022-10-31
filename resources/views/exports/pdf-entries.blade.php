<table class="table table-striped- table-bordered table-hover table-checkable">
    <thead>
        <tr class="text-uppercase">
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
            <td>
                {{ $entry->surveyed->name ?? '' }}
            </td>
            <td>
                {{ $entry->surveyed->vat_number ?? '' }}
            </td>
            <td>
                <a href="{{ route('survey.entry', ['entryId' => $entry->id])}}">{{
                    $entry->participant }}</a>
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