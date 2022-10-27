@component('survey::questions.base', compact('question'))
{{-- Admin view --}}
@php($listQuestions = $lang ? $question->getTranslation('options',$lang) : $question->options)
@foreach($listQuestions as $option)
<div class="custom-control custom-radio">
    <input type="radio" wire:model.defer="answers.{{$question->id}}.value" name="{{ $question->key }}"
        id="{{ $question->key . '-' . Str::slug($option) }}" value="{{ $option }}" class="custom-control-input" {{
        ($disabled ?? false) ? 'disabled' : '' }}>
    <label class="custom-control-label" for="{{ $question->key . '-' . Str::slug($option) }}">
        {{ $option }}
    </label>
</div>
@endforeach
@if($question->comments)
<input type="text" wire:model="comments.{{$question->id}}" class="form-control mt-2">
@endif
@if($this->errorsBag ?? false)
@if(in_array($question->id, $this->errorsBag))
<span class="text-danger">Campo requerido</span>
@endif
@endif
@endcomponent