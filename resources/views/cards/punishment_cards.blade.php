<div class="stats-grid">
    <div class="stat-card status-filter" data-status="all">
        <div class="stat-card-header">
            <div class="stat-card-title">{{ __('messages.total_uploaded_education') }}</div>
            <div class="stat-card-icon"><i class="fas fa-gavel"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['total_uploaded'] ?? 0 }}</div>
    </div>

    <div class="stat-card status-filter" data-status="approved">
        <div class="stat-card-header">
            <div class="stat-card-title">{{ __('messages.approved') }} (Approved)</div>
            <div class="stat-card-icon"><i class="fas fa-circle-check text-success"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['approved'] ?? 0 }}</div>
    </div>

    <div class="stat-card status-filter" data-status="rejected">
        <div class="stat-card-header">
            <div class="stat-card-title">{{ __('messages.rejected') }} (Rejected)</div>
            <div class="stat-card-icon"><i class="fas fa-circle-xmark text-danger"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['rejected'] ?? 0 }}</div>
    </div>

    <div class="stat-card status-filter" data-status="uploaded">
        <div class="stat-card-header">
            <div class="stat-card-title">{{ __('messages.pending') }} (Pending)</div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half text-warning"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>
</div>
