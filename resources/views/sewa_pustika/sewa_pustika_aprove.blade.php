@php
    $designation = Session::get('user.designation_type');
@endphp

<!-- Sewapustika Approval Modal / View -->
<div class="modal-body">
    <!-- Police & Master Data Card -->
    <div class="card mb-3">
        <div class="card-header bg-secondary text-white">{{ __('messages.police_details') }}</div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.police_name') }}:</strong> {{ $polices->police_name ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.buckle_number') }}:</strong> {{ $polices->buckle_number ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.role_designation') }}:</strong> {{ $polices->role ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.post') }}:</strong> {{ $polices->post ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.state') }}:</strong> {{ $polices->state_name ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.district') }}:</strong> {{ $polices->district_name ?? '--' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-6"><strong>{{ __('messages.city') }}:</strong> {{ $polices->city_name ?? '--' }}</div>
                <div class="col-md-6"><strong>{{ __('messages.police_station') }}:</strong> {{ $polices->police_station_name ?? '--' }}</div>
            </div>

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6"><strong>{{ __('messages.status') }}:</strong> {{ $polices->sewa_pustika_status ?? '--' }}</div>
                    <div class="col-md-6"><strong>{{ __('messages.review_status') }}:</strong> {{ $polices->review_status ?? __('messages.pending') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>{{ __('messages.document') }}:</strong>
                        @if ($polices->sewapusticapath)
                            <a href="{{ route('sewapustika.view', $polices->sewapusticapath) }}" target="_blank">
                                {{ __('messages.view_download') }}
                            </a>
                        @else
                            {{ __('messages.not_uploaded') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval Form -->
        <form id="sewaPustikaApprovalForm" action="{{ route('sewapustika.approve.store') }}" method="POST">
            @csrf
            <input type="hidden" name="sewapustika_id" value="{{ $polices->sewapustika_id ?? '' }}">

            <!-- Action -->
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

            <!-- Remark -->
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
</div>
