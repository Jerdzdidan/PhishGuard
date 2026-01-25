
<x-modals.creation-and-update-modal 
    id="add-or-update-modal"
    title="New Admin"
    action=""
    submitButtonName="Submit"
>


{{-- Name --}}
<div class="col-12 form-control-validation">
    <x-input.input-field
        id="first_name" 
        name="first_name" 
        label="First name"
        type="text"
        icon="bx bx-user-circle" 
        placeholder="First name" 
        help=""
    />
    <x-input.input-field
        id="last_name" 
        name="last_name" 
        label="Last name"
        type="text"
        icon="bx bx-user-circle" 
        placeholder="Last name" 
        help=""
    />
</div>

{{-- Email and Password --}}
<div class="col-sm-12 form-control-validation">
    <x-input.input-field
        id="email" 
        name="email" 
        label="Email"
        type="text"
        icon="bx bx-id-card" 
        placeholder="Email" 
        help=""
    />

    <x-input.select-field
        id="user_type"
        label="User Type"
        icon="fa-solid fa-tags"
        :options="[
            ['value' => 'ADMIN', 'text' => 'Admin'],
            ['value' => 'USER', 'text' => 'User'],
        ]"
        placeholder="Select User Type"
    />
    
    <x-input.password-field
        id="password" 
        name="password" 
        label="Password" 
        icon="bx bx-lock-alt" 
        placeholder="*******"
        help=""
    />
</div>

</x-modals.creation-and-update-modal>