@php($question = $surveyQuestion->question)
<div class="@if(!$is_child) form-group @endif">
    <label style="font-size:1.1rem">{{ $numberQuestion }}. {!! $question->content
        !!}</label>@if($surveyQuestion->mandatory)*@endif:
    @php($listQuestions = 'es' ? $question->getTranslation('options','es') : $question->options)
    @if(!empty($listQuestions))
    @if(!is_array($surveyQuestion->question->options))
    @php($optionsForeach = json_decode($listQuestions,true))
    @else
    @php($optionsForeach = $listQuestions)
    @endif
    @foreach($optionsForeach['es'] as $option)
    <div class="custom-control custom-radio">
        <input type="radio" class="custom-control-input">
        <label class="custom-control-label">
            {{ $option }}
        </label>
    </div>
    @endforeach
    @else
    <br>
    <label>Pendiente de definir opciones</label>
    @endif
    @if(count($surveyQuestion->children) > 0) 
    <label style="margin-left: 20px; margin-top: 20px;"">En caso que la pregunta {{ $numberQuestion }} sea respondida</label>
    @foreach($surveyQuestion->children as $key => $surveyQuestionChild)
    <div class="col-12" style="
    margin-left: 20px;
    page-break-after:auto;
    page-break-before:auto;">
        @include("survey::exports.pdf-survey-types.{$surveyQuestionChild->question->type}",
        [
        'surveyQuestion' => $surveyQuestionChild,
        'numberQuestion' => $numberQuestion . '.' .($key+1),
        'value' => '',
        'is_child' => true])
    </div>
    @endforeach
    @endif
</div>