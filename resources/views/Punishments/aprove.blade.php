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
    <input type="hidden" name="punishment_id" value="{{ $punishment->punishment_id ?? '' }}">

    <!-- ✅ Action -->
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

    <!-- ✅ Remark (Single Field, Optional) -->
    <div class="mb-3">
        <label for="remark" class="form-label">Remark (optional)</label>
        <input type="text" name="remark" id="remark" class="form-control" placeholder="Enter remark (if any)">
        <small class="form-text text-muted">You can provide a remark for both approval and rejection.</small>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-success">Submit</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>

