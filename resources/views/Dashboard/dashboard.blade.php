@extends('Dashboard.header')
<style>/* ------------------------
   DASHBOARD LAYOUT
---------------------------*/
.dashboard-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ------------------------
   STATISTICS CARDS
---------------------------*/
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 1.5rem;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: 0.2s;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-card-title {
    font-size: 0.9rem;
    color: #666;
    font-weight: 500;
}

.stat-card-icon i {
    font-size: 1.5rem;
    color: #f97316;
}

.stat-card-value {
    font-size: 1.5rem;
    font-weight: bold;
    margin-top: 10px;
}

/* ------------------------
   CONTENT GRID
---------------------------*/
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

/* Content Card */
.content-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 1.5rem;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.content-card h3 {
    margin-bottom: 15px;
    font-size: 1.1rem;
    color: #333;
}

/* Activity Items */
.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
}

.activity-dot {
    width: 10px;
    height: 10px;
    background: #f97316;
    border-radius: 50%;
    margin-top: 5px;
}

/* Quick Action Buttons */
.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f97316;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 1rem;
    cursor: pointer;
    margin-bottom: 10px;
    transition: 0.2s;
    font-weight: 500;
}

.quick-action-btn i {
    font-size: 1rem;
}

.quick-action-btn:hover {
    background: #ea580c;
}

.quick-action-btn.secondary {
    background: #e5e7eb;
    color: #333;
}

.quick-action-btn.secondary:hover {
    background: #d1d5db;
}

/* ------------------------
   RESPONSIVE DESIGN
---------------------------*/
@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
}
</style>
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
