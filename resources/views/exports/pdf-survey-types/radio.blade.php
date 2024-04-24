@php($question = $surveyQuestion->question)
<div class="form-group">
    <label style="font-size:1.1rem">{{ $numberQuestion }} {!! $question->content
        !!}</label>@if($surveyQuestion->mandatory)*@endif:
    @php($listQuestions = 'es' ? $question->getTranslation('options','es') : $question->options)
    @foreach($listQuestions as $option)
    <div class="custom-control custom-radio">
        <input type="radio" class="custom-control-input">
        <label class="custom-control-label">
            {{ $option }}
        </label>
    </div>
    @endforeach
</div>