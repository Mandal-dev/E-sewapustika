<div class="stats-grid">
   

    <!-- Total Rewards Uploaded -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">एकूण अपलोड Reward</div>
            <div class="stat-card-icon"><i class="fas fa-upload"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['total_uploaded'] ?? 0 }}</div>
    </div>

    <!-- Approved -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Approved</div>
            <div class="stat-card-icon"><i class="fas fa-circle-check"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['approved'] ?? 0 }}</div>
    </div>

    <!-- Rejected -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Rejected</div>
            <div class="stat-card-icon"><i class="fas fa-circle-xmark"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['rejected'] ?? 0 }}</div>
    </div>

    <!-- Pending -->
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Pending</div>
            <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
        </div>
        <div class="stat-card-value">{{ $stats['pending'] ?? 0 }}</div>
    </div>
</div>
