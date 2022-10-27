<div>
    <div class="card mt-5">
        <div class="card-header bg-white p-4">
            <h2 class="mb-0">{{ $this->survey->name }}</h2>
        </div>
        @foreach($this->survey->sections as $index => $section)
        @include('survey::sections.single')
        @endforeach
    </div>
    @if (session()->has('answersAlert'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <span> {{ session('answersAlert') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if($this->survey->status == MattDaneshvar\Survey\Library\Constants::SURVEY_STATUS_PROCESS && $sendForm)
    <div class="d-flex flex-row-reverse">
        @if ($this->entry->lang == 'en')
        <button class="btn btn-success my-3 mr-2"
            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
            wire:click="sendAnswers">
            Send
        </button>
        <button class="btn btn-primary my-3 mr-2" wire:click="saveAnswers">
            Save
        </button>
        @else
        <button class="btn btn-success my-3"
            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
            wire:click="sendAnswers">
            Enviar
        </button>
        <button class="btn btn-primary my-3 mr-2" wire:click="saveAnswers">
            Guardar
        </button>
        @endif
    </div>
    @endif
</div>