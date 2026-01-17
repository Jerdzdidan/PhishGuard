<!-- Updated admin_creation_modal.html -->
<div class="offcanvas offcanvas-end" id="{{ $id }}">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="ModalLabel">{{ $title }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-grow-1">
        <form 
            class="{{ $formClass ?? 'add-or-update-form' }}"
            id="{{ $formId ?? 'add-or-update-form' }}"
            action="{{ $action }}"
            enctype="{{ $enctype ?? '' }}"
        >
            @csrf

            <input type="hidden" name="id" id="id" value="">

            {{ $slot }}

            <!-- Form Actions -->
            <div class="col-sm-12 pt-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary data-submit flex-fill">
                        {{ $submitButtonName }}
                    </button>
                    <button type="reset" class="btn btn-outline-primary flex-fill" data-bs-dismiss="offcanvas">
                        Cancel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>