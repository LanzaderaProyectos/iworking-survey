<div>
    <div class="card mt-5">
        <div class="card-header bg-white p-4">
            <div class="row">
                <div class="col-10">
                    <h1 class="mb-0 mt-2">{{ $this->survey->name }}</h1>
                    @if($this->entry ?? false)
                    <h3 class="mt-2"> {{ $this->entry->surveyed->name ?? '' }} - {{ $this->entry->surveyed->contact_person ?? '' }}
                    </h3>
                    @endif
                </div>
                <div class="col-2 text-right">
                    <img src="{{ asset('/img/logo_rubio.jpg') }}" style="max-height: 70px;" />
                </div>
            </div>
        </div>
        @foreach($this->survey->sections as $index => $section)
        @include('survey::sections.single')
        @endforeach
    </div>
    @if($this->survey->status == MattDaneshvar\Survey\Library\Constants::SURVEY_STATUS_PROCESS && $sendForm)
    @if (session()->has('answersAlert'))
    <div id="answersAlert" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <span> {{ session('answersAlert') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="d-flex flex-row-reverse">
        @if ($this->entry->lang == 'en')
        <button id="send-en" class="btn btn-success my-3 mr-2"
            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
            wire:click="sendAnswers">
            Send
        </button>
        <button class="btn btn-primary my-3 mr-2" wire:click="saveAnswers">
            Save
        </button>
        @else
        <button id="send-es" class="btn btn-success my-3"
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