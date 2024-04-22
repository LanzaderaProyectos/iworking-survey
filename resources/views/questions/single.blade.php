@if ($filter)
@php($subSurveyQuestions = $this->getSubQuestionsAfterAnswer($surveyQuestion))
@else
@php($subSurveyQuestions = $surveyQuestion->children)
@endif
@if(count($subSurveyQuestions) > 0)

@foreach ($subSurveyQuestions as $key=>$subSurveyQuestion)
@if ((isset($showEntry) && $this->answers[$subSurveyQuestion->id]['value'] != '') || !isset($showEntry))
<div class="pl-4 py-4" wire:key="{{str()->random(5)}}">
    @include(view()->exists("survey::questions.types.{$subSurveyQuestion->question->type}")
    ? "survey::questions.types.{$subSurveyQuestion->question->type}"
    : 'survey::questions.types.text',
    [
    'disabled' => $disabled ?? false,
    'lang' => $this->lang ?? false,
    'value' => $lastEntry ? $lastEntry->answerFor($subSurveyQuestion) : null,
    'includeResults' => ($lastEntry ?? null) !== null,
    'surveyQuestion' => $subSurveyQuestion,
    'numberQuestion' => $parentKey.'.'.($key+1),
    ]
    )

    @if (!$sendForm)
    @include('survey::questions.single', ['surveyQuestion' => $subSurveyQuestion, 'filter' => false,'parentKey' => $parentKey.'.'.($key+1)])
    @else
    @if (isset($this->respondedQuestions[$subSurveyQuestion->id]))
    @include('survey::questions.single', ['surveyQuestion' => $subSurveyQuestion, 'filter' => false,'parentKey' => $parentKey.'.'.($key+1)])
    @endif
    @endif
</div>
@endif

@endforeach
@endif