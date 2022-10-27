@extends(config('iworking-survey.iworking-layout') . 'layouts.app', [
'pageTitle' => 'Listado de pedidos',
'pageBreadcrumbs' => [
'Pedidos',
'Listado',
]
])

@section('content')
@livewire('iworking-survery::show-entry')
@endsection