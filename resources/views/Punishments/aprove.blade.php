<!-- Modal Body -->
<div class="modal-body">
    <!-- Punishment Details Card -->
    <div class="card mb-3">
        <div class="card-header bg-secondary text-white">Details</div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6"><strong>Police Name:</strong> {{ $punishment->police_name ?? '--' }}</div>
                <div class="col-md-6"><strong>Buckle Number:</strong> {{ $punishment->buckle_number ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Designation:</strong> {{ $punishment->role ?? '--' }}</div>
                <div class="col-md-6"><strong>Post:</strong> {{ $punishment->post ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>State:</strong> {{ $punishment->state_name ?? '--' }}</div>
                <div class="col-md-6"><strong>District:</strong> {{ $punishment->district_name ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>City:</strong> {{ $punishment->city_name ?? '--' }}</div>
                <div class="col-md-6"><strong>Punishment Date:</strong> {{ $punishment->punishment_given_date ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>Type:</strong> {{ $punishment->punishment_type ?? '--' }}</div>
                <div class="col-md-6"><strong>Reason:</strong> {{ $punishment->reason ?? '--' }}</div>
            </div>
        </div>
    </div>
<!-- Approval Form -->
<form id="punishmentApprovalForm" action="{{ route('punishments.approve.store') }}" method="POST">
    @csrf
    <input type="hidden" name="punishment_id" value="{{ $punishment->id ?? '' }}">

    <div class="mb-3">
        <label class="form-label">Action</label><br>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="status_approve" value="Approved" required>
            <label class="form-check-label" for="status_approve">Approve</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="status_reject" value="Rejected">
            <label class="form-check-label" for="status_reject">Reject</label>
        </div>
    </div>

    <!-- Dynamic Remark Field -->
    <div class="mb-3">
        <label for="remark" class="form-label" id="remark_label">Remark</label>
        <input type="text" name="remark" id="remark" class="form-control"
               placeholder="Enter remark" required>
        <small class="form-text text-muted" id="remark_help">
            Please enter appropriate remark based on your selection
        </small>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-success">Submit</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<!-- JavaScript for dynamic label update -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusApprove = document.getElementById('status_approve');
    const statusReject = document.getElementById('status_reject');
    const remarkLabel = document.getElementById('remark_label');
    const remarkInput = document.getElementById('remark');
    const remarkHelp = document.getElementById('remark_help');

    function updateRemarkField() {
        if (statusApprove.checked) {
            remarkLabel.textContent = 'Gadget Number';
            remarkInput.placeholder = 'Enter gadget number';
            remarkHelp.textContent = 'Please enter the gadget number for approval';
        } else if (statusReject.checked) {
            remarkLabel.textContent = 'Reject Reason';
            remarkInput.placeholder = 'Enter rejection reason';
            remarkHelp.textContent = 'Please provide reason for rejection';
        } else {
            remarkLabel.textContent = 'Remark';
            remarkInput.placeholder = 'Enter remark';
            remarkHelp.textContent = 'Please enter appropriate remark based on your selection';
        }
    }

    // Add event listeners
    statusApprove.addEventListener('change', updateRemarkField);
    statusReject.addEventListener('change', updateRemarkField);

    // Initialize
    updateRemarkField();
});
</script>
