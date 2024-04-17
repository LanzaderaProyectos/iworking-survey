@component('survey::questions.base',[
    'question' => $question,
    'numberQuestion' => $numberQuestion
    ])
    <input type="number" wire:model="answers.{{$question->id}}.value"  name="{{ $question->key }}" id="{{ $question->key }}" class="form-control" value="{{ $value ?? old($question->key) }}" {{ ($disabled ?? false) ? 'disabled' : '' }}>
    
    @slot('report')
        @if($includeResults ?? false)
            {{ number_format((new \MattDaneshvar\Survey\Utilities\Summary($question))->average()) }} (Average)
        @endif
    @endslot
@endcomponent