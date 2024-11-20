@component('survey::questions.base', [
'surveyQuestion' => $surveyQuestion,
'numberQuestion' => $numberQuestion
])
<div>
    <input wire:key="{{ $surveyQuestion->id }}" 
            type="number" 
            name="{{ $surveyQuestion->question->key }}"
            class="form-control"
            value="{{ $value ?? old($surveyQuestion->question->key) }}" 
            {{ ($disabled ?? false) ? 'disabled' : '' }}
            wire:model.change="answers.{{$surveyQuestion->id}}.value"
            wire:key="{{ $surveyQuestion->id }}">
</div>
@slot('report')
@if($includeResults ?? false)
{{ number_format((new \MattDaneshvar\Survey\Utilities\Summary($question))->average()) }} (Average)
@endif
@endslot
@if($this->errorsBag ?? false)
@if(in_array($surveyQuestion->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent