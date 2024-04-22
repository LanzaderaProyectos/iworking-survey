<div class="form-group">
    <label style="font-size:1.1rem" class="mb-3" for="{{ $surveyQuestion->question->key }}">{{ $numberQuestion }} {!! $surveyQuestion->question->content !!}</label>@if($surveyQuestion->mandatory)*@endif:
    {{ $slot }}
</div>