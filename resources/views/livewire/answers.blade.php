<div wire:ignore.self>
@include('survey::standard', ['survey' => $survey,
'sendForm' => true,
'disabled' => $disabled,])
</div>