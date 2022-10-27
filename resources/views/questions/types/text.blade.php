@component('survey::questions.base', compact('question'))
<input type="text" wire:model.defer="answers.{{$question->id}}" name="{{ $question->key }}" id="{{ $question->key }}"
    class="form-control" value="{{ $value ?? old($question->key) }}" {{ ($disabled ?? false) ? 'disabled' : '' }}>
@if($this->errorsBag ?? false)
@if(in_array($question->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent