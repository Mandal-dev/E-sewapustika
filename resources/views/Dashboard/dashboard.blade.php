@extends('Dashboard.header')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('data')
<div class="dashboard-content">

    <!-- -----------------------------
         STATISTICS CARDS
    ------------------------------- -->
    <div class="stats-grid">
        <!-- Total Police -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">एकूण पोलीस</div>
                <div class="stat-card-icon"><i class="fas fa-user-shield"></i></div>
            </div>
            <div class="stat-card-value"></div>
        </div>

        <!-- Total Rewards Uploaded -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">एकूण अपलोड </div>
                <div class="stat-card-icon"><i class="fas fa-upload"></i></div>
            </div>
            <div class="stat-card-value"></div>
        </div>

        <!-- Approved -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">Approved</div>
                <div class="stat-card-icon"><i class="fas fa-circle-check"></i></div>
            </div>
            <div class="stat-card-value"></div>
        </div>

        <!-- Rejected -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">Rejected</div>
                <div class="stat-card-icon"><i class="fas fa-circle-xmark"></i></div>
            </div>
            <div class="stat-card-value"></div>
        </div>

        <!-- Pending -->
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">Pending</div>
                <div class="stat-card-icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
            <div class="stat-card-value"></div>
        </div>
    </div>

@endsection
