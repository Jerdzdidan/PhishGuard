<label class="form-label" for="{{ $id }}">{{ $label }}</label>
<div class="input-group input-group-merge">
    @if($icon)
    <span class="input-group-text"><i class="{{ $icon }}"></i></span>
    @endif
    <input type="password" id="{{ $id }}" name="{{ $name }}" class="form-control" placeholder="{{ $placeholder ?? '' }}" />
    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
</div>
<div class="invalid-feedback"></div>
@if($help)
    <div class="form-text">{{ $help ?? '' }}</div>
@endif