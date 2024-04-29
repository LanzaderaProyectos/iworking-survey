@php($question = $surveyQuestion->question)
<div class="@if(!$is_child) form-group @endif">
    <label style="font-size:1.1rem" class="mb-3">{{ $numberQuestion }}. {!! $question->content !!}</label>@if($surveyQuestion->mandatory)*@endif:
    <br>
    <br>
    <br>
    <br>
    <br>
</div>