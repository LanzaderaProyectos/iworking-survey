@component('survey::questions.base', [
'question' => $question,
'numberQuestion' => $numberQuestion
])
<textarea wire:key="{{str()->random(5)}}" wire:model="answers.{{$question->id}}.value" name="{{ $question->key }}" id="{{ $question->key }}" class="form-control" {{ ($disabled ?? false) ? 'disabled' : '' }} rows="5" style="resize: vertical" wire:model="answers.{{$question->id}}.value" >{{ $value ?? old($question->key) }}</textarea>
@if($this->errorsBag ?? false)
@if(in_array($question->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent