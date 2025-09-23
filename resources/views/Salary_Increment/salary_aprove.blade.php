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

<!-- Modal Form -->
<form id="rewardForm" method="POST" action="{{ route('salary.increment.approve') }}">
    @csrf
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
                        @if($salary->increment_documents)
                            <p><b>Documents:</b>
                                <a href="{{ asset('uploads/salary_docs/' . $salary->increment_documents) }}" target="_blank" class="link-primary">View</a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Review & Actions -->
        <div class="card shadow-sm mt-3">
            <div class="card-header"><strong>Review & Actions</strong></div>
            <div class="card-body">
                <div class="form-group radio-group">
                    <label><input type="radio" name="status" value="Approved" required> Approve</label>
                    <label><input type="radio" name="status" value="Rejected"> Reject</label>
                </div>

                <!-- Gadget number (for approved) -->
                <div class="form-group" id="gadget_field" style="display:none;">
                    <label>Add Gadget Number</label>
                    <input type="text" name="gadget_no" class="form-control" placeholder="Enter gadget number">
                </div>

                <!-- Reject reason -->
                <div class="form-group" id="remark_field" style="display:none;">
                    <label>Reject Reason</label>
                    <input type="text" name="remark" class="form-control" placeholder="Enter rejection reason">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
        <button type="submit" class="btn btn-submit">Submit</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</form>

<script>
    (function() {
        const form = document.getElementById('rewardForm');
        const gadgetField = document.getElementById('gadget_field');
        const remarkField = document.getElementById('remark_field');
        const radios = form.querySelectorAll('input[name="status"]');

        function toggleFields() {
            const selected = form.querySelector('input[name="status"]:checked');
            if(!selected) return;

            if(selected.value === 'Approved') {
                gadgetField.style.display = 'block';
                remarkField.style.display = 'none';
                gadgetField.querySelector('input').required = true;
                remarkField.querySelector('input').required = false;
            } else if(selected.value === 'Rejected') {
                gadgetField.style.display = 'none';
                remarkField.style.display = 'block';
                gadgetField.querySelector('input').required = false;
                remarkField.querySelector('input').required = true;
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', toggleFields);
        });

        // Reset on modal close
        document.querySelector('.btn-close').addEventListener('click', () => {
            gadgetField.style.display = 'none';
            remarkField.style.display = 'none';
            gadgetField.querySelector('input').value = '';
            remarkField.querySelector('input').value = '';
        });
    })();
</script>
