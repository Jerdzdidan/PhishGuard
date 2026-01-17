
<x-modals.creation-and-update-modal 
    id="add-or-update-modal"
    title="New Lesson"
    action=""
    submitButtonName="Submit"
>

{{-- Start Year and End Year --}}
<div class="col-sm-12 form-control-validation">
    <x-input.input-field
        id="title" 
        name="title" 
        label="Title"
        type="text"
        icon="menu-icon tf-icons bx bx-heading" 
        placeholder="Title" 
        help=""
    />
</div>

<div class="col-sm-12 form-control-validation">
    <x-input.text-area-field
        id="description"
        icon="menu-icon tf-icons bx bxs-captions"
        label="Description"
        placeholder="Enter lesson description"
        rows="3"
    />
</div>

<div class="col-sm-12 form-control-validation">
    <x-input.integer-field 
        id="time"
        label="Time"
        icon="menu-icon tf-icons bx bx-timer"
        placeholder="Time (in minutes)"
        :min="0"
        :step="1"
        help=""
    />
</div>

{{-- Semester --}}
<div class="col-sm-12 form-control-validation mb-2">
    <x-input.select-field
        id="difficulty"
        label="Difficulty"
        icon="menu-icon tf-icons bx bx-tachometer"
        :options="[
            ['value' => 'EASY', 'text' => 'EASY'],
            ['value' => 'MEDIUM', 'text' => 'MEDIUM'],
            ['value' => 'HARD', 'text' => 'HARD'],
        ]"
        placeholder="Select Category"
    />
</div>

</x-modals.creation-and-update-modal>