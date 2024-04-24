@component('survey::questions.base', [
'surveyQuestion' => $surveyQuestion,
'numberQuestion' => $numberQuestion
])

@if(!empty($surveyQuestion->question->options))
@foreach ($surveyQuestion->question->options as $keyOption => $option)
<div class="custom-control custom-checkbox">
    <input type="checkbox" wire:key="multiple-{{ $keyOption }}-{{$surveyQuestion->id}}" wire:model.live="answers.{{$surveyQuestion->id}}.value" name="{{ $surveyQuestion->question->key }}[]" id="{{ $surveyQuestion->question->key . '-' . Str::slug($option) }}" value="{{ $option }}" class="custom-control-input" {{
        ($value ?? old($surveyQuestion->question->key)) == $option ? 'checked' : '' }}
    {{ ($disabled ?? false) ? 'disabled' : '' }}
    >
    <label class="custom-control-label" for="{{ $surveyQuestion->question->key . '-' . Str::slug($option) }}">{{ $option }}
    </label>
</div>
@endforeach
@else
<br>
<label>Pendiente de definir opciones</label>
@endif
@if($this->errorsBag ?? false)
@if(in_array($surveyQuestion->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent