<div id="accordion" wire:key="{{str()->random(5)}}">
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed d-flex align-items-center" style="gap: 15px; text-decoration: none !important;" data-toggle="collapse"
                    data-target="#collapseSection{{ $index }}" aria-expanded="true"
                    aria-controls="collapseSection{{ $index }}">
                    <span class="h3">{{ $section->name }}
                    </span>
                    <i class="fas fa-chevron-up tab-arrow"></i>
                </button>
            </h5>
        </div>
        <div id="collapseSection{{ $index }}" class="collapse show" aria-labelledby="headingTwo"
            data-parent="#accordion" wire:ignore.self>
            <div class="card-body">
                @foreach ($section->surveyQuestionsMain as $key => $surveyQuestionMain)
                {{-- @dd($section->surveyQuestionsMain) --}}
                    <div class="p-4 border-bottom">
                        @include(view()->exists("survey::questions.types.{$surveyQuestionMain->question->type}")
                                ? "survey::questions.types.{$surveyQuestionMain->question->type}"
                                : 'survey::questions.types.text',
                            [
                                'disabled' => $disabled ?? false,
                                'lang' => $this->lang ?? false,
                                'value' => $lastEntry ? $lastEntry->answerFor($mainQuestion) : null,
                                'includeResults' => ($lastEntry ?? null) !== null,
                                'surveyQuestion' => $surveyQuestionMain,
                                'numberQuestion' => $key + 1 . '.',
                            ]
                        )
                        @if (!$sendForm)
                            @include('survey::questions.single', ['surveyQuestion' => $surveyQuestionMain, 'filter' => false,'parentKey' => $key+1])
                        @else
                            @if (isset($this->respondedQuestions[$surveyQuestionMain->id]))
                                @include('survey::questions.single', ['surveyQuestion' => $surveyQuestionMain, 'filter' => true,'parentKey' => $key+1])
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
