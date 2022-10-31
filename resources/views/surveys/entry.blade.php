@extends(config('iworking-survey.iworking-layout') . 'layouts.app', [
'pageTitle' => 'Entrada'
])

@section('content')
@livewire('iworking-survery::show-entry')
@endsection