@php($question = $surveyQuestion->question)
<div class="@if(!$is_child) form-group @endif">
    <label style="font-size:1.1rem">{{ $numberQuestion }}. {!! $question->content
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
    @if(count($surveyQuestion->children) > 0)
    @foreach($surveyQuestion->children as $key => $surveyQuestionChild)
    @if($surveyQuestionChild->condition == "NO")
    <label style="margin-left: 20px; margin-top: 20px;"">Si la respuesta es No</label>
    @elseif($surveyQuestionChild->condition == "SI")
    <label style="margin-left: 20px; margin-top: 20px;"">Si la respuesta es Si</label>
    @elseif($surveyQuestionChild->condition == "NA")
    <label style="margin-left: 20px; margin-top: 20px;"">Si la respuesta es Na</label>
    @else
    <label style="margin-left: 20px; margin-top: 20px;"">Si se ha respuesto</label>
    @endif
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