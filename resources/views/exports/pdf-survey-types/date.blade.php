@php($question = $surveyQuestion->question)
<div class="form-group">
    <label style="font-size:1.1rem">{{ $numberQuestion }} {!! $question->content
        !!}</label>@if($surveyQuestion->mandatory)*@endif:
    {{$value}}
</div>