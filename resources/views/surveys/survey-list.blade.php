@extends(config('iworking-survey.iworking-layout') . 'layouts.app', [
'pageTitle' => 'Listado de encuestas',
'pageBreadcrumbs' => [
'Encuestas',
'Listado',
]
])

@section('content')
<div class="kt-portlet kt-portlet--mobile">
    <div class="kt-portlet__head kt-portlet__head--lg">
        <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="kt-font-brand fas fa-shopping-cart fa-lg"></i>
            </span>
            <h3 class="kt-portlet__head-title text-uppercase">
                Listado de encuestas
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="row mt-2">
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link active" id="order-orders-tab" data-toggle="tab" href="#order-orders"
                            role="tab" aria-controls="order-orders" aria-selected="true">Encuestas</a>
                    </li>
                    <li class="nav-item" role="presentation" wire:ignore>
                        <a class="nav-link" id="order-draft-tab" data-toggle="tab" href="#order-draft" role="tab"
                            aria-controls="order-draft" aria-selected="true">
                            @if(Auth::user()->hasAnyRole(['admin']))
                            Listado de borradores
                            @else
                            Mis borradores
                            @endif
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="orderContent">
                    <div class="tab-pane fade show active" id="order-orders" role="tabpanel"
                        aria-labelledby="order-orders-tab" wire:ignore.self>
                        @livewire('iworking-survery::survey-list',[
                        'task' => false,
                        'status' => $status ?? null,
                        'admin' => $admin ?? false
                        ])
                    </div>
                    <div class="tab-pane fade" id="order-draft" role="tabpanel" aria-labelledby="order-lines-tab"
                        wire:ignore.self>
                        @livewire('iworking-survery::survey-list',[
                        'task' => true,
                        'status' => $status ?? null,
                        'admin' => $admin ?? false,
                        'draft' => true,
                        ])
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection