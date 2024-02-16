@if ($filter)
    @php($subQuestions = $this->getSubQuestionsAfterAnswer($question))
@else
    @php($subQuestions = $question->subQuestions)
@endif
@foreach ($subQuestions as $subQuestion)
    @if ((isset($showEntry) && $this->answers[$subQuestion->id]['value'] != '') || !isset($showEntry))
        <div class="pl-4 py-4" wire:key="{{str()->random(5)}}">
            @include(view()->exists("survey::questions.types.{$subQuestion->type}")
                    ? "survey::questions.types.{$subQuestion->type}"
                    : 'survey::questions.types.text',
                [
                    'disabled' => $disabled ?? false,
                    'lang' => $this->lang ?? false,
                    'value' => $lastEntry ? $lastEntry->answerFor($subQuestion) : null,
                    'includeResults' => ($lastEntry ?? null) !== null,
                    'question' => $subQuestion,
                    'numberQuestion' => '-',
                ]
            )
            @if (!$sendForm)
                @include('survey::questions.single', ['question' => $subQuestion])
            @else
                @if (isset($this->respondedQuestions[$subQuestion->id]))
                    @include('survey::questions.single', ['question' => $subQuestion])
                @endif
            @endif
        </div>
    @endif

@endforeach
