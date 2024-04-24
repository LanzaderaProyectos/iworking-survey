@php($question = $surveyQuestion->question)
<div class="form-group">
    <label style="font-size:1.1rem" class="mb-3">{{ $numberQuestion }} {!! $question->content
        !!}</label>@if($surveyQuestion->mandatory)*@endif:
    @if(empty($value))
    {{number_format((int)$value,2,',','')}}
    @else
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;,&nbsp;&nbsp;&nbsp;€
    @endif
</div>