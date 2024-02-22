<table>
    <thead>
        <tr>
            <th>
                Nombre
            </th>
            <th>
                Tipo
            </th>
            <th>
                Fecha creación
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($questions as $question)
            <tr>
                <td>
                    {{ $question->getTranslation('content', 'es') }}
                </td>
                <td>
                    {{ $question->type }}
                </td>
                <td>
                    {{ auth()->user()->applyDateFormat($survey->created_at) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
