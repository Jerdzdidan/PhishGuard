<label class="form-label" for="{{ $id }}">{{ $label }}</label>
<div class="input-group input-group-merge mb-2">
    @if($icon)
    <span class="input-group-text"><i class="{{ $icon ?? '' }}"></i></span>
    @endif
    <input type="{{ $type ?? 'text' }}" id="{{ $id }}" name="{{ $name }}" class="form-control" placeholder="{{ $placeholder ?? '' }}" aria-label="{{ $label }}" value="{{ $value ?? '' }}" />
</div>
<div class="invalid-feedback"></div>
@if($help)
    <div class="form-text">{{ $help ?? '' }}</div>
@endif