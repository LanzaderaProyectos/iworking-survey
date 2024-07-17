<div wire:ignore.self class="modal fade" id="reject_modal" tabindex="-1" role="dialog"
    aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Motivo Modificación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            @if (session()->has('errorResponse'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('errorResponse') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            @endif
                            <textarea
                                class="form-control" wire:model="rejectReason" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('backend.forms.close')</button>
                    <button type="button" class="btn btn-primary" wire:click="saveRejectMessage" wire:loading.attr="disabled">Confirmar</button>
                </div>
            </div>
        </div>
    </div>
</div>