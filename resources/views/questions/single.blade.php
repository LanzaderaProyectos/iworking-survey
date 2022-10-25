<div class="p-4 border-bottom">
    @include(view()->exists("survey::questions.types.{$question->type}")
    ? "survey::questions.types.{$question->type}"
    : "survey::questions.types.text",[
    'disabled' => false,
    'value' => $lastEntry ? $lastEntry->answerFor($question) : null,
    'includeResults' => ($lastEntry ?? null) !== null
    ]
    )
</div>