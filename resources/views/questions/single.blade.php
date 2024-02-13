    @foreach ($question->subQuestions as $subQuestion)
        <div class="pl-4 py-4">
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
    @endforeach
