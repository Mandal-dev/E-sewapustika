@extends('Dashboard.header')
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@section('data')
<div class="dashboard-content">
    <h4 class="mb-4">Dashboard</h4>

    <div class="stats-grid">
        @foreach($cards as $card)
        <div class="stat-card">
            <!-- Card Header -->
            <div class="stat-card-header">
                <div class="stat-card-title">{{ $card['title'] }}</div>
                <div class="stat-card-icon">
                    @if($card['title'] == 'Salary Increment') <i class="fas fa-upload"></i>
                    @elseif($card['title'] == 'Rewards') <i class="fas fa-gift"></i>
                    @elseif($card['title'] == 'Punishments') <i class="fas fa-gavel"></i>
                    @elseif($card['title'] == 'Sewa Pustika') <i class="fas fa-book"></i>
                    @endif
                </div>
            </div>

            <!-- Stats Values -->
            <div class="stat-card-value">
                <div class="d-flex justify-content-between">
                    <span>एकूण पोलीस:</span>
                    <span>{{ $card['total_police'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>अपलोड:</span>
                    <span>{{ $card['total_uploaded'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Approved:</span>
                    <span class="text-success">{{ $card['approved'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Rejected:</span>
                    <span class="text-danger">{{ $card['rejected'] ?? 0 }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Pending:</span>
                    <span class="text-warning">{{ $card['pending'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
