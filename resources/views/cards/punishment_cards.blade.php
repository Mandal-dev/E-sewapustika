<div class="stats-grid">
    <!-- Total Police -->
    <!-- Total Punishments Uploaded -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">एकूण अपलोड शिक्षा</div>
            <div class="stat-card-icon"><i class="fas fa-gavel"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['total_uploaded'] ?? 0 }}</div>
    </div>

    <!-- Approved -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">मंजूर (Approved)</div>
            <div class="stat-card-icon"><i class="fas fa-circle-check text-success"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['approved'] ?? 0 }}</div>
    </div>

    <!-- Rejected -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">नाकारलेले (Rejected)</div>
            <div class="stat-card-icon"><i class="fas fa-circle-xmark text-danger"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['rejected'] ?? 0 }}</div>
    </div>

    <!-- Pending -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">प्रलंबित (Pending)</div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half text-warning"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>
</div>
