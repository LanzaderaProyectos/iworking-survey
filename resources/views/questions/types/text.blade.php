@component('survey::questions.base', compact('question'))
<input type="text" wire:model.defer="answers.{{$question->id}}" name="{{ $question->key }}" id="{{ $question->key }}"
    class="form-control" value="{{ $value ?? old($question->key) }}" {{ ($disabled ?? false) ? 'disabled' : '' }}>
@endcomponent