<label class="form-label" for="{{ $id }}">
    {{ $label }}
    @if($required)
        <span class="text-danger">*</span>
    @endif
</label>
<div class="input-group input-group-merge mb-2">
    @if($icon)
    <span class="input-group-text"><i class="{{ $icon }}"></i></span>
    @endif
    <input 
        type="number" 
        id="{{ $id }}" 
        name="{{ $name ?? $id }}" 
        class="form-control" 
        placeholder="{{ $placeholder }}" 
        aria-label="{{ $label }}"
        value="{{ old($name ?? $id, $value) }}"
        @if($min !== null) min="{{ $min }}" @endif
        @if($max !== null) max="{{ $max }}" @endif
        step="{{ $step }}"
        @if($required) required @endif
        inputmode="numeric"
        pattern="[0-9]*"
    />
</div>
<div class="invalid-feedback"></div>
@if($help)
    <div class="form-text">{{ $help }}</div>
@endif