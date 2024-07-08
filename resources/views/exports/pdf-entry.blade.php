<style>
    .img-container {
        position: fixed;
        top: 0px;
        right: 0px;
    }
</style>
<div style="width: 100%">
    <img class="img-container" src="{{ asset('/img/logo_rubio.jpg') }}" style="max-height: 70px;" />
</div>
<h1 style="margin-top: 40px">{{ $survey->getTranslation('name', $lang) }}</h1>
<h3 style="color:grey"> {{ $entry->surveyed->contact_person }}&nbsp;&nbsp;&nbsp;&nbsp; {{ date('d-m-Y')}}</h3>
@foreach($answers as $index => $answer)
<p>{{ $index + 1}}. {!! $answer->question->getTranslation('content',$lang) !!}</p>
<p>{{ $answer->value }}</p>
@endforeach