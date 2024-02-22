@extends(config('iworking-survey.iworking-layout') . 'layouts.app', [
    'pageTitle' => 'Listado de preguntas',
    'pageBreadcrumbs' => ['Preguntas', 'Formulario'],
])

@section('content')
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title text-uppercase">
                    Pregunta
                </h3>
            </div>
        </div>
            <div class="row mt-2">
                <div class="col-12">
                        @livewire('iworking-questions::question-edit', [
                            'questionId' => $question ?? null,
                        ])
                </div>
            </div>
    </div>
@endsection
