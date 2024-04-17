<table>
    <thead>
        <tr>
            <th>
                Nº Formulario
            </th>
            <th>
                Nombre
            </th>
            <th>
                Autor
            </th>
            <th>
                Estado
            </th>
            <th>
                Fecha creación
            </th>
            <th>
                Vencimiento
            </th>
            <th>
                Puntuación media
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($surveys as $survey)
        <tr>
            <td>
                {{ $survey->survey_number }}
            </td>
            <td>
                {{ $survey->name }}
            </td>
            <td>
                {{ $survey->user->first_name }} {{ $survey->user->last_name }}
            </td>
            <td>
                @lang('survey::status.survey.'.$survey->status ?? '')
            </td>
            <td>
                {{ auth()->user()->applyDateFormat($survey->created_at) }}
            </td>
            <td>
                {{ auth()->user()->applyDateFormat($survey->expiration) }}
            </td>
            <td>
                {{
                number_format($survey->entries->sum('sum_score') / $survey->entries->count(), 2, ',',
                '.')
                }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>