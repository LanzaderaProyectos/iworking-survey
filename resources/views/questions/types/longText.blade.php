@component('survey::questions.base', [
'surveyQuestion' => $surveyQuestion,
'numberQuestion' => $numberQuestion
])
<textarea wire:key="{{str()->random(5)}}" wire:model="answers.{{$surveyQuestion->id}}.value" name="{{ $surveyQuestion->key }}"
    id="{{ $surveyQuestion->question->key }}" class="form-control" {{ ($disabled ?? false) ? 'disabled' : '' }} rows="5"
    style="resize: vertical">{{ $value ?? old($surveyQuestion->question->key) }}</textarea>
@if($this->errorsBag ?? false)
@if(in_array($surveyQuestion->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif

@endcomponent