<label class="form-label" for="{{ $id }}">{{ $label }}</label>
<div class="input-group input-group-merge">
    @if($icon)
    <span class="input-group-text"><i class="icon-base {{ $icon }}"></i></span>
    @endif
    <textarea id="{{ $id }}" name="{{ $id }}" class="form-control" rows="{{ $rows }}" placeholder="{{ $placeholder }}" aria-label="{{ $label }}"></textarea>
</div>