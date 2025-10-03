<div class="stats-grid">
    <!-- Total Sewa Pustika Uploaded -->
    <div class="stat-card status-filter" data-status="all">
        <div class="stat-card-header">
            <div class="stat-card-title">अपलोड सेवा पुस्तिका</div>
            <div class="stat-card-icon"><i class="fas fa-book"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['total_uploaded'] ?? 0 }}</div>
    </div>

    <!-- Approved -->
     <div class="stat-card status-filter" data-status="approved">
        <div class="stat-card-header">
            <div class="stat-card-title">Approved</div>
            <div class="stat-card-icon"><i class="fas fa-circle-check text-success"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['approved'] ?? 0 }}</div>
    </div>

    <!-- Rejected -->
     <div class="stat-card status-filter" data-status="rejected">
        <div class="stat-card-header">
            <div class="stat-card-title">Rejected</div>
            <div class="stat-card-icon"><i class="fas fa-circle-xmark text-danger"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['rejected'] ?? 0 }}</div>
    </div>

    <!-- Pending -->
     <div class="stat-card status-filter" data-status="uploaded">
        <div class="stat-card-header">
            <div class="stat-card-title">Pending</div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half text-warning"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>
</div>
