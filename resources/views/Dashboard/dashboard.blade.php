@extends('Dashboard.header')
 <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

@section('data')
<div class="dashboard-content">

    <!-- -----------------------------
         STATISTICS CARDS
    ------------------------------- -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">एकूण पोलीस ठाणे</div>
                <div class="stat-card-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-card-value">{{ $total_police_thane }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">एकूण पोलीस</div>
                <div class="stat-card-icon"><i class="fas fa-user-shield"></i></div>
            </div>
            <div class="stat-card-value">{{ $total_police }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">अपलोड सेवापुस्तिका</div>
                <div class="stat-card-icon"><i class="fas fa-book"></i></div>
            </div>
            <div class="stat-card-value">{{ $total_pustika }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-title">एकूण अपलोड पगारवाढ</div>
                <div class="stat-card-icon"><i class="fas fa-calendar"></i></div>
            </div>
            <div class="stat-card-value">{{ $total_salary_increments }}</div>
        </div>
    </div>

    <!-- -----------------------------
         MAIN CONTENT GRID
    ------------------------------- -->
    <div class="content-grid">

        <!-- Activity Feed -->
        <div class="content-card">
            <h3>अलीकडील क्रियाकलाप</h3>
            <div class="activity-item">
                <div class="activity-dot"></div>
                <div>
                    <strong>नवीन अधिकारी नोंदणी</strong><br>
                    <small style="color: #7f8c8d;">२ मिनिटांपूर्वी</small>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-dot"></div>
                <div>
                    <strong>पोलीस ठाण्याचा अहवाल सबमिट</strong><br>
                    <small style="color: #7f8c8d;">१५ मिनिटांपूर्वी</small>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-dot"></div>
                <div>
                    <strong>रजा विनंती प्रलंबित</strong><br>
                    <small style="color: #7f8c8d;">१ तासापूर्वी</small>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-dot"></div>
                <div>
                    <strong>पगारवाढ प्रक्रिया पूर्ण</strong><br>
                    <small style="color: #7f8c8d;">३ तासांपूर्वी</small>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="content-card">
            <h3>जलद कृती</h3>
            <button class="quick-action-btn">
                <i class="fas fa-user-plus"></i>
                नवीन अधिकारी जोडा
            </button>
            <button class="quick-action-btn secondary">
                <i class="fas fa-file-alt"></i>
                अहवाल तयार करा
            </button>
            <button class="quick-action-btn secondary">
                <i class="fas fa-calendar-plus"></i>
                मिटिंग शेड्युल करा
            </button>
            <button class="quick-action-btn secondary">
                <i class="fas fa-bell"></i>
                सूचना पाठवा
            </button>
        </div>

    </div>

    <!-- -----------------------------
         OFFICER PROFILE CARD
         Example below using your CSS
    ------------------------------- -->
@endsection
