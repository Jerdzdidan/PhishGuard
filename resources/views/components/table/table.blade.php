<div class="table-responsive">
    <table id="{{ $id }}" class="table table-striped table-hover" style="width:100%">
        <thead class="bg-body-secondary">
            <tr>
                {{ $slot }}
            </tr>
        </thead>
        <tbody class="table-group-divider" style="border-top: 2px solid #9c9c9c !important;">
            <!-- Data will be loaded via AJAX -->
        </tbody>
    </table>
</div>