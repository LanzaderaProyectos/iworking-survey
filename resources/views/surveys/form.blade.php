@extends(config('iworking.iworking-layout') . 'layouts.app', [
'pageTitle' => (isset($order->id)) ? 'Editar: ' . $order->order_number : 'Crear encuesta',
'pageBreadcrumbs' => [
'Encuestas',
]
])

@section('content')
<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand fas fa-shopping-cart fa-lg"></i>
            </span>
            <h3 class="kt-portlet__head-title text-uppercase">
                {{ (isset($order->id)) ? 'Editar encuesta ' . $order->order_number : 'Crear encuesta' }}
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <div class="kt-portlet__head-wrapper">
                <div class="kt-portlet__head-actions">
                    <a href="{{ route('survey.list') }}" class="btn btn-brand btn-elevate btn-sm">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>
    <div class="kt-portlet__body">
        @livewire('iworking-survery::create-survey')
    </div>
</div>
@endsection

@push('css')

@endpush