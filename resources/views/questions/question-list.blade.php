@extends(config('iworking-survey.iworking-layout') . 'layouts.app', [
    'pageTitle' => 'Listado de preguntas',
    'pageBreadcrumbs' => ['Preguntas', 'Listado'],
])

@section('content')
    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                </span>
                <h3 class="kt-portlet__head-title text-uppercase">
                    Listado de preguntas
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">
                        <a href="{{ route('questions.new') }}" class="btn btn-primary btn-sm btn-icon-sm"
                            data-toggle="tooltip" data-placement="top" title="Create">
                            <i class="fas fa-plus"></i>
                            Nueva pregunta
                        </a>
                    </div>
                </div>
            </div>
        </div>
            <div class="row mt-2">
                <div class="col-12">
                        @livewire('iworking-questions::question-list')
                </div>
            </div>
    </div>
@endsection
