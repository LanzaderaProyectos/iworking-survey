@component('survey::questions.base', [
'surveyQuestion' => $surveyQuestion,
'numberQuestion' => $numberQuestion
])
<input type="date" wire:key="{{ $surveyQuestion->id }}" wire:model.live="answers.{{$surveyQuestion->id}}.value" name="{{ $surveyQuestion->question->key }}" id="{{ $surveyQuestion->question->key }}"
    class="form-control" value="{{ $value ?? old($surveyQuestion->question->key) }}" {{ ($disabled ?? false) ? 'disabled' : '' }}>

@slot('report')
@if($includeResults ?? false)
{{ number_format((new \MattDaneshvar\Survey\Utilities\Summary($surveyQuestion))->average()) }} (Average)
@endif
@endslot
@if($this->errorsBag ?? false)
@if(in_array($surveyQuestion->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent