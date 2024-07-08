<div class="form-group">
    <label style="font-size:1.1rem" class="mb-3" for="{{ $question->key }}">{{ $numberQuestion }}. {!! $question->content !!}</label>    
    {{ $slot }}
</div>