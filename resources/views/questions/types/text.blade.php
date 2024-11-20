@component('survey::questions.base', [
'surveyQuestion' => $surveyQuestion,
'numberQuestion' => $numberQuestion
])
<div>
<input type="text" wire:key="{{ $surveyQuestion->id }}" wire:model.change="answers.{{$surveyQuestion->id}}.value" name="{{ $surveyQuestion->question->key }}" id="{{ $surveyQuestion->id }}"
    class="form-control" value="{{ $value ?? old($surveyQuestion->question->key) }}" {{ ($disabled ?? false) ? 'disabled' : '' }} >
</div>
@if($this->errorsBag ?? false)
@if(in_array($surveyQuestion->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent