<label for="{{ $id }}" class="form-label">{{ $label }}</label>
<input type="file" 
        class="form-control" 
        id="{{ $id }}" 
        name="{{ $name }}" 
        accept="{{ $accept }}"
        required>
<div class="form-text">{{ $helptext ?? '' }}</div>