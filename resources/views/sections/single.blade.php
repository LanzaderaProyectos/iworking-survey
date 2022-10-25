<div id="accordion">
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseSection{{$index}}"
                    aria-expanded="false" aria-controls="collapseSection{{$index}}">
                    <span class="h3">{{ $section->name }}
                    </span>
                </button>
            </h5>
        </div>
        <div id="collapseSection{{$index}}" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
            <div class="card-body">
                @foreach($section->questions as $question)
                @include('survey::questions.single')
                @endforeach
            </div>
        </div>
    </div>
</div>