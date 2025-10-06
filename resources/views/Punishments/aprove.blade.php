<!-- Modal Body -->
<div class="modal-body">
    <!-- Punishment Details Card -->
    <div class="card mb-3">
        <div class="card-header bg-secondary text-white">{{ __('messages.details') }}</div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.police_name') }}:</strong> {{ $punishment->police_name ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.buckle_number') }}:</strong> {{ $punishment->buckle_number ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.designation') }}:</strong> {{ $punishment->role ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.post') }}:</strong> {{ $punishment->post ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.state') }}:</strong> {{ $punishment->state_name ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.district') }}:</strong> {{ $punishment->district_name ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.city') }}:</strong> {{ $punishment->city_name ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.punishment_date') }}:</strong> {{ $punishment->punishment_given_date ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.type') }}:</strong> {{ $punishment->punishment_type ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.reason') }}:</strong> {{ $punishment->reason ?? '--' }}</div>
            </div>
        </div>
    </div>

    <!-- Approval Form -->
    <form id="punishmentApprovalForm" action="{{ route('punishments.approve.store') }}" method="POST">
        @csrf
        <input type="hidden" name="punishment_id" value="{{ $punishment->punishment_id ?? '' }}">

        <!-- ✅ Action -->
        <div class="mb-3">
            <label class="form-label">{{ __('messages.action') }}</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status" id="status_approve" value="Approved" required>
                <label class="form-check-label" for="status_approve">{{ __('messages.approve') }}</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="status" id="status_reject" value="Rejected">
                <label class="form-check-label" for="status_reject">{{ __('messages.reject') }}</label>
            </div>
        </div>

        <!-- ✅ Remark (Single Field, Optional) -->
        <div class="mb-3">
            <label for="remark" class="form-label">{{ __('messages.remark_optional') }}</label>
            <input type="text" name="remark" id="remark" class="form-control" placeholder="{{ __('messages.enter_remark') }}">
            <small class="form-text text-muted">{{ __('messages.remark_note') }}</small>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success">{{ __('messages.submit') }}</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
        </div>
    </form>
</div>
