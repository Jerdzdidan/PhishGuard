
<x-modals.creation-and-update-modal 
    id="add-or-update-modal"
    title="New Item"
    action=""
    submitButtonName="Submit"
>

{{-- Image Upload --}}
<div class="col-12 form-control-validation">
    <label for="image" class="form-label">Scenario Image <span class="text-danger">*</span></label>
    <input type="file" name="image" id="image" class="form-control" accept="image/*">
    <small class="text-muted">Upload a screenshot of a phishing email, website, or message</small>
    <div id="imagePreview" class="mt-2" style="display:none;">
        <img id="previewImg" src="" class="img-fluid rounded" style="max-height: 200px;">
    </div>
</div>

{{-- Title --}}
<div class="col-12 form-control-validation">
    <x-input.input-field
        id="title" 
        name="title" 
        label="Title"
        type="text"
        icon="bx bx-file" 
        placeholder="e.g. Suspicious Email" 
        help=""
    />
</div>

{{-- Description --}}
<div class="col-12 form-control-validation">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control" rows="2" placeholder="Brief context for this scenario"></textarea>
</div>

{{-- Question --}}
<div class="col-12 form-control-validation">
    <label for="content" class="form-label">Question <span class="text-danger">*</span></label>
    <textarea name="content" id="content" class="form-control" rows="2" placeholder="e.g. What should you do when you receive this message?" required></textarea>
</div>

{{-- Multiple Choice Options --}}
<div class="col-12 form-control-validation">
    <label class="form-label">Answer Options <span class="text-danger">*</span></label>
    <div id="optionsContainer">
        <div class="input-group mb-2 option-row">
            <div class="input-group-text">
                <input type="radio" name="correct_option" value="0" class="form-check-input" title="Mark as correct">
            </div>
            <input type="text" class="form-control" name="options[]" placeholder="Option A" required>
            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)" style="display:none;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="input-group mb-2 option-row">
            <div class="input-group-text">
                <input type="radio" name="correct_option" value="1" class="form-check-input" title="Mark as correct">
            </div>
            <input type="text" class="form-control" name="options[]" placeholder="Option B" required>
            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)" style="display:none;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addOption()">
            <i class="fa-solid fa-plus me-1"></i> Add Option
        </button>
        <small class="text-muted"><i class="bx bx-radio-circle-marked me-1"></i> Radio = correct answer</small>
    </div>
</div>

</x-modals.creation-and-update-modal>

<script>
// Image preview
document.getElementById('image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removeOption(btn) {
    $(btn).closest('.option-row').remove();
    // Re-index radio values
    $('.option-row').each(function(i) {
        $(this).find('input[type="radio"]').val(i);
    });
}

function addOption() {
    const index = $('.option-row').length;
    const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $('#optionsContainer').append(`
        <div class="input-group mb-2 option-row">
            <div class="input-group-text">
                <input type="radio" name="correct_option" value="${index}" class="form-check-input" title="Mark as correct">
            </div>
            <input type="text" class="form-control" name="options[]" placeholder="Option ${letters[index]}" required>
            <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    `);
}
</script>
