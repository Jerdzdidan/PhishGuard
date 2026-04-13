
<x-modals.creation-and-update-modal 
    id="add-or-update-modal"
    title="New Section"
    action=""
    submitButtonName="Submit"
>

{{-- Section Name --}}
<div class="col-12 form-control-validation">
    <x-input.input-field
        id="name" 
        name="name" 
        label="Section Name"
        type="text"
        icon="bx bx-group" 
        placeholder="e.g. BSCS 3A" 
        help=""
    />
</div>

{{-- Description --}}
<div class="col-12 form-control-validation">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional description for the section"></textarea>
</div>

</x-modals.creation-and-update-modal>
