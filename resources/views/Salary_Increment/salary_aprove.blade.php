<!-- ============================
 Reward Approval Modal Styles
 ============================ -->
<style>
    /* Modal Header */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #0d6efd;
        color: #fff;
        padding: 14px 18px;
        border-bottom: 1px solid #ddd;
    }

    .modal-header h5 {
        font-size: 18px;
        margin: 0;
        font-weight: bold;
    }

    /* Cards */
    .card {
        border-radius: 8px;
    }

    .card-header {
        font-weight: 600;
        background: #f5f5f5;
    }

    .card-body p {
        margin: 6px 0;
        font-size: 14px;
        color: #333;
    }

    .card-body b {
        color: #000;
    }

    /* Footer buttons */
    .modal-footer {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 14px 18px;
        border-top: 1px solid #ddd;
        background: #fafafa;
    }

    .btn-submit {
        background: #28a745;
        color: #fff;
        font-weight: 500;
        border-radius: 6px;
    }

    .btn-approve {
        background: #198754;
        color: #fff;
        font-weight: 500;
        border-radius: 6px;
    }

    .btn-reject {
        background: #dc3545;
        color: #fff;
        font-weight: 500;
        border-radius: 6px;
    }

    .btn-close-white {
        filter: invert(1);
    }

    /* Form fields for gadget/reject reason */
    .form-group {
        margin-top: 12px;
    }

    .form-group label {
        font-weight: 500;
        font-size: 14px;
    }

    .form-control {
        border-radius: 6px;
        padding: 8px 10px;
        font-size: 14px;
    }
</style>


    <div class="modal-header">
        <h5 class="modal-title">Reward Approval</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <div class="row g-3">
            <!-- Police Details -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header"><strong>Police Details</strong></div>
                    <div class="card-body">
                        <p><b>Name:</b> {{ $salary->police_name }}</p>
                        <p><b>Buckle No:</b> {{ $salary->buckle_number }}</p>
                        <p><b>Post:</b> {{ $salary->post }}</p>
                        <p><b>Role:</b> {{ $salary->role }}</p>
                        <p><b>District:</b> {{ $salary->district_name }}</p>
                        <p><b>City:</b> {{ $salary->city_name }}</p>
                    </div>
                </div>
            </div>
            <input type="hidden" name="salary_id" value="{{ $salary->salary_id }}">

            <!-- Salary Increment Details -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header"><strong>Salary Increment Details</strong></div>
                    <div class="card-body">
                        <p><b>Type:</b> {{ $salary->increment_type }}</p>
                        <p><b>Date:</b> {{ $salary->increment_date }}</p>
                        <p><b>New Salary:</b> <span class="text-success">₹{{ $salary->new_salary }}</span></p>
                        <p><b>Level:</b> {{ $salary->level }}</p>
                        <p><b>Grade Pay:</b> {{ $salary->grade_pay }}</p>
                        <p><b>Increase:</b> <span class="text-success">₹{{ $salary->increased_amount }}</span></p>
                        <p><b>Present Days:</b> {{ $salary->present_days ?? '--' }}</p>
                        @if ($salary->increment_documents)
                            <p><b>Documents:</b>
                                <a href="{{ route('salary_increment.view', $salary->increment_documents) }}"
                                    target="_blank" class="link-primary">View</a>
                            </p>
                        @endif
                    </div>
                </div>

            </div>

                <!-- Approval Form -->
                <form id="salaryApprovalForm" action="{{ route('salary.increment.approve') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="salary_id" value="{{ $salary->salary_id }}">

                    <!-- Action Radios -->
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

                    <!-- Remark -->
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark (optional)</label>
                        <input type="text" name="remark" id="remark" class="form-control" placeholder="Enter remark (if any)">
                        <small class="form-text text-muted">You can provide a remark for both approval and rejection.</small>
                    </div>

                    <!-- Form Buttons -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                < /form>
   </div>
