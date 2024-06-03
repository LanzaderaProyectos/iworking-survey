<div class="col-12">
    <div class="card mt-5">
        <div class="card-header bg-white p-4">
            <div class="row">
                <div class="col-10">
                    <h1 class="mb-0 mt-2">{{ $survey->name }}</h1>
                    @if ($this->entry ?? false)
                    <h3 class="mt-2 text-muted"> {{ $this->entry->surveyed->name ?? '' }} -
                        {{ $this->entry->surveyed->contact_person ?? '' }}
                    </h3>
                    @endif
                </div>
                <div class="col-2 text-right">
                    <button wire:click="exportSurveyToPDF" class="btn btn-danger rounded-right pl-3 pr-2" type="button"
                        data-toggle="tooltip" data-placement="top" title="Exportar tabla a PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
        </div>
        {{-- @include('survey::sections.profesional') --}}
        @php($numberQuestion = 1)
        @foreach ($survey->sections as $index => $section)
        @include('survey::sections.single')
        @php($numberQuestion += $section->questions->count())
        @endforeach
        @if($survey->has_order ?? false)
        {{-- //TODO: change !== 0 to == 0 --}}
        @if(empty($entry))
        @include('survey::sections.pharmaciesSale')
        @else
        @livewire('projects.partials.entry-order',['entry' => $entry])
        @endif
        @endif
        @if($survey->has_promotional_material ?? false)
        {{-- //TODO: change !== 0 to == 0 --}}
        @if(empty($entry))
        @include('survey::sections.promotionalMaterial')
        @else
        @livewire('projects.partials.entry-promotional-materials',['entry' => $entry])
        @endif
        @endif
    </div>
    @if ($survey->status == MattDaneshvar\Survey\Library\Constants::SURVEY_STATUS_PROCESS && $sendForm || true)
    @if (session()->has('answersAlert'))
    <div id="answersAlert" class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <span> {{ session('answersAlert') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="d-flex flex-row-reverse">
        @if (!empty($this->entry) && $this->entry->lang == 'en')
        <button id="send-en" class="btn btn-success my-3 mr-2"
            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
            wire:click="sendAnswers">
            Send
        </button>
        <button class="btn btn-primary my-3 mr-2" wire:click="saveAnswers">
            Save
        </button>
        @else
        {{-- <button id="send-es" class="btn btn-success my-3"
            onclick="confirm('¿Está seguro? Esta acción no puede deshacerse.') || event.stopImmediatePropagation();"
            wire:click="sendAnswers">
            Enviar
        </button> --}}
        <button class="btn btn-primary my-3 mr-2" wire:click="saveAnswers">
            Guardar
        </button>
        @endif
    </div>
    @endif
    <style>
        .collapsed .tab-arrow {
            transform: rotate(180deg);
            transition: transform 0.3s;
        }

        .tab-arrow {
            transition: transform 0.3s;

        }
    </style>
</div>