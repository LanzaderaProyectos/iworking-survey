@component('survey::questions.base', [
'surveyQuestion' => $surveyQuestion,
'numberQuestion' => $numberQuestion
])
{{-- Admin view --}}
@php($listQuestions = $lang ? $surveyQuestion->question->getTranslation('options',$lang) : $surveyQuestion->question->options)

@if(!is_array($listQuestions))
@php($listQuestions = json_decode($listQuestions,true))
@endif
@foreach($listQuestions['es'] as $option)
<div class="custom-control custom-radio" wire:key="{{str()->random(5)}}">
    <input type="radio" wire:model.live="answers.{{$surveyQuestion->id}}.value" name="{{ $surveyQuestion->question->key }}"
        id="{{ $surveyQuestion->question->key . '-' . Str::slug($option) }}" value="{{ $option }}" class="custom-control-input" {{
        ($disabled ?? false) ? 'disabled' : '' }}>
    <label class="custom-control-label" for="{{ $surveyQuestion->question->key . '-' . Str::slug($option) }}">
        {{ $option }}
    </label>
</div>
@endforeach
@if($surveyQuestion->question->comments)
<input type="text" wire:model="comments.{{$surveyQuestion->id}}" class="form-control mt-2">
@endif
@if($this->errorsBag ?? false)
@if(in_array($surveyQuestion->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent