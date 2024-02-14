<div id="accordion">
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse"
                    data-target="#collapseSection{{ $index }}" aria-expanded="true"
                    aria-controls="collapseSection{{ $index }}">
                    <span class="h3">{{ $section->name }}
                    </span>
                </button>
            </h5>
        </div>
        <div id="collapseSection{{ $index }}" class="collapse show" aria-labelledby="headingTwo"
            data-parent="#accordion" wire:ignore.self>
            <div class="card-body">
                @foreach ($section->mainQuestions as $key => $mainQuestion)
                    <div class="p-4 border-bottom">
                        @include(view()->exists("survey::questions.types.{$mainQuestion->type}")
                                ? "survey::questions.types.{$mainQuestion->type}"
                                : 'survey::questions.types.text',
                            [
                                'disabled' => $disabled ?? false,
                                'lang' => $this->lang ?? false,
                                'value' => $lastEntry ? $lastEntry->answerFor($mainQuestion) : null,
                                'includeResults' => ($lastEntry ?? null) !== null,
                                'question' => $mainQuestion,
                                'numberQuestion' => $key + 1 . '.',
                            ]
                        )
                        @if (!$sendForm)
                            @include('survey::questions.single', ['question' => $mainQuestion, 'filter' => false])
                        @else
                            @if (isset($this->respondedQuestions[$mainQuestion->id]))
                                @include('survey::questions.single', ['question' => $mainQuestion, 'filter' => true])
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
