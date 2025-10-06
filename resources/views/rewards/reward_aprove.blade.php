<!-- ============================
 Reward Approval Modal Styles
 ============================ -->
<style>
    /* Modal Header */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f5f7fa;
        padding: 14px 18px;
        border-bottom: 1px solid #ddd;
    }

    .modal-header h2 {
        font-size: 18px;
        margin: 0;
        font-weight: bold;
    }

    /* Body */
    .modal-body {
        padding: 18px;
        text-align: left;
    }

    .details p {
        margin: 6px 0;
        font-size: 14px;
        color: #333;
    }

    .details strong {
        color: #000;
    }

    /* Form Groups */
    .form-group {
        margin: 14px 0;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #444;
    }

    .form-group input[type="text"] {
        width: 100%;
        padding: 8px 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
        transition: 0.2s;
    }

    .form-group input[type="text"]:focus {
        border-color: #007bff;
        outline: none;
    }

    /* Radio group centered */
    .radio-group {
        display: flex;
        justify-content: center;
        gap: 30px;
        margin: 15px 0;
    }

    .radio-group label {
        font-size: 14px;
        cursor: pointer;
    }

    /* Buttons */
    .btn {
        padding: 7px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        transition: 0.3s;
        font-weight: 500;
    }

    .btn-view {
        background: #444;
        color: #fff;
    }

    .btn-download {
        background: #007bff;
        color: #fff;
        margin-left: 5px;
    }

    .btn-submit {
        background: #28a745;
        color: #fff;
    }

    .btn-secondary {
        background: #f1f1f1;
        color: #333;
        margin-left: 8px;
        border: 1px solid #ddd;
    }

    .btn i {
        margin-right: 6px;
    }

    .btn:hover {
        opacity: 0.9;
    }

    /* Footer */
    .modal-footer {
        display: flex;
        justify-content: center;
        gap: 10px;
        padding: 14px 18px;
        border-top: 1px solid #ddd;
        background: #fafafa;
    }
</style>

<!-- Modal Header -->
<div class="modal-header">
    <h2>{{ __('messages.reward_approval') }}</h2>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.close') }}"></button>
</div>

<!-- Form Start -->
<form action="{{ route('reward.review.store') }}" method="POST">
    @csrf
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding-right: 15px;">
        <!-- Officer Details -->
        <div class="details">
            <div class="row mb-2">
                <div class="col-6 text-start"><strong>{{ __('messages.officer_name') }}:</strong></div>
                <div class="col-6 text-start">{{ $police->police_name }}</div>
            </div>
            <input type="hidden" name="reward_id" value="{{ $police->id }}">
            <div class="row mb-2">
                <div class="col-6 text-start"><strong>{{ __('messages.station') }}:</strong></div>
                <div class="col-6 text-start">{{ $police->city_name }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 text-start"><strong>{{ __('messages.buckle_number') }}:</strong></div>
                <div class="col-6 text-start">{{ $police->buckle_number }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 text-start"><strong>{{ __('messages.designation') }}:</strong></div>
                <div class="col-6 text-start">{{ $police->role }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 text-start"><strong>{{ __('messages.reward_type') }}:</strong></div>
                <div class="col-6 text-start">{{ $police->reward_type }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 text-start"><strong>{{ __('messages.reward_reason') }}:</strong></div>
                <div class="col-6 text-start">{{ $police->reason }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 text-start"><strong>{{ __('messages.reward_date') }}:</strong></div>
                <div class="col-6 text-start">{{ $police->reward_given_date }}</div>
            </div>
            <div class="row mb-2 align-items-center">
                <div class="col-6 text-start"><strong>{{ __('messages.document') }}:</strong></div>
                <div class="col-6 text-start">
                    @if ($police->rewards_documents)
                        <a href="{{ asset('uploads/' . $police->rewards_documents) }}" target="_blank"
                            class="btn btn-view"><i class="bi bi-eye"></i> {{ __('messages.view') }}</a>
                        <a href="{{ asset('uploads/' . $police->rewards_documents) }}" download
                            class="btn btn-download"><i class="bi bi-download"></i> {{ __('messages.download') }}</a>
                    @else
                        <span class="text-muted">{{ __('messages.no_document') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <h5 class="text-center mt-3"><strong>{{ __('messages.action') }}</strong></h5>

        <!-- Radio buttons -->
        <div class="form-group radio-group">
            <label><input type="radio" name="status" value="Approved" required> {{ __('messages.approve') }}</label>
            <label><input type="radio" name="status" value="Rejected"> {{ __('messages.reject') }}</label>
        </div>

        <!-- Gadget field (shown if approved) -->
        <div class="form-group" id="gadget_field" style="display: none;">
            <label>{{ __('messages.add_gadget_no') }}</label>
            <input type="text" name="gadget_no" placeholder="{{ __('messages.enter_gadget_number') }}" id="gadget_no">
        </div>

        <!-- Remark field (shown if rejected) -->
        <div class="form-group" id="remark_field" style="display: none;">
            <label>{{ __('messages.remark_if_rejected') }}</label>
            <input type="text" name="remark" placeholder="{{ __('messages.type_reason') }}" id="remark">
        </div>
    </div>

    <!-- Footer -->
    <div class="modal-footer">
        <button type="submit" class="btn btn-submit"><i class="bi bi-check2-circle"></i> {{ __('messages.submit') }}</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> {{ __('messages.cancel') }}</button>
    </div>
</form>

<!-- Script -->
<script>
(function() {
    function el(id) { return document.getElementById(id); }

    function toggleFieldsByValue(val) {
        const gadgetField = el('gadget_field');
        const remarkField = el('remark_field');
        const gadgetInput = el('gadget_no');
        const remarkInput = el('remark');
        if (!gadgetField || !remarkField) return;

        const v = (val || '').toLowerCase();
        if (v === 'approved') {
            gadgetField.style.display = 'block';
            remarkField.style.display = 'none';
            if (gadgetInput) gadgetInput.required = true;
            if (remarkInput) remarkInput.required = false;
        } else if (v === 'rejected') {
            gadgetField.style.display = 'none';
            remarkField.style.display = 'block';
            if (gadgetInput) gadgetInput.required = false;
            if (remarkInput) remarkInput.required = true;
        } else {
            gadgetField.style.display = 'none';
            remarkField.style.display = 'none';
            if (gadgetInput) gadgetInput.required = false;
            if (remarkInput) remarkInput.required = false;
        }
    }

    document.addEventListener('change', function(e) {
        const t = e.target;
        if (t && t.matches('input[name="status"]')) {
            toggleFieldsByValue(t.value);
        }
    }, true);

    function initBootstrapModalWithId(modalSelector) {
        const modal = document.querySelector(modalSelector);
        if (!modal) return;
        modal.addEventListener('shown.bs.modal', function() {
            const checked = modal.querySelector('input[name="status"]:checked');
            toggleFieldsByValue(checked ? checked.value : null);
        });
        modal.addEventListener('hidden.bs.modal', function() {
            toggleFieldsByValue(null);
            const g = el('gadget_no'), r = el('remark');
            if (g) g.value = '';
            if (r) r.value = '';
        });
    }

    initBootstrapModalWithId('#rewardApprovalModal');
})();
</script>
