
<x-modals.creation-and-update-modal 
    id="add-or-update-modal"
    title="New Scenario"
    action=""
    submitButtonName="Submit"
>

{{-- Lesson --}}
<div class="col-12 form-control-validation">
    <label for="lesson_id" class="form-label">Lesson</label>
    <select name="lesson_id" id="lesson_id" class="form-select" required>
        <option value="">Select Lesson</option>
        @foreach(\App\Models\Lesson::where('has_simulation', true)->get() as $lesson)
            <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
        @endforeach
    </select>
</div>

{{-- Title --}}
<div class="col-12 form-control-validation">
    <x-input.input-field
        id="title" 
        name="title" 
        label="Title"
        type="text"
        icon="bx bx-file" 
        placeholder="e.g. Phishing Detection Simulation" 
        help=""
    />
</div>

{{-- Type --}}
<div class="col-12 form-control-validation">
    <label for="type" class="form-label">Type</label>
    <select name="type" id="type" class="form-select" required>
        <option value="simulation">Simulation</option>
        <option value="pre_assessment">Pre-Assessment</option>
        <option value="post_assessment">Post-Assessment</option>
    </select>
</div>

{{-- Description --}}
<div class="col-12 form-control-validation">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
</div>

</x-modals.creation-and-update-modal>
