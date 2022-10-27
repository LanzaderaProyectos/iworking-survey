<div id="accordion">
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSection{{$index}}"
                    aria-expanded="true" aria-controls="collapseSection{{$index}}">
                    <span class="h3">{{ $section->name }}
                    </span>
                </button>
            </h5>
        </div>
        <div id="collapseSection{{$index}}" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion" wire:ignore.self>
            <div class="card-body">
                @foreach($section->questions as $question)
                <div class="p-4 border-bottom">
                    @include(view()->exists("survey::questions.types.{$question->type}")
                    ? "survey::questions.types.{$question->type}"
                    : "survey::questions.types.text",[
                    'disabled' => $disabled ?? false,
                    'lang' => $this->lang ?? false,
                    'value' => $lastEntry ? $lastEntry->answerFor($question) : null,
                    'includeResults' => ($lastEntry ?? null) !== null
                    ]
                    )
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>