@extends('Dashboard.header')

@section('data')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/calender.css') }}">

<div class="app-content mt-4">
    <div class="dashboard-content">
        <h3 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Attendance Calendar</h3>

        <div class="list">
            <div class="content-card p-3 shadow-sm rounded">
                <div class="report-card-header mb-3 d-flex justify-content-between align-items-center">
                    <div class="report-card-title"><i class="fas fa-calendar me-2"></i>Calendar</div>
                    <div class="actions-row mb-3">
                        @if (!isset($singleUserId))
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <button id="checkInBtn" class="btn btn-success">
                                <i class="fas fa-sign-in-alt me-2"></i>Check In
                            </button>
                            <button id="checkOutBtn" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Check Out
                            </button>

                            <h6 class="ms-auto mb-0">🕒 Current Time (India):
                                <span id="currentTime" class="fw-bold text-primary"></span>
                            </h6>
                        </div>
                        @endif
                        <input type="date" id="attendanceDate" class="form-control" hidden>
                        <div id="message" class="mt-2"></div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button id="prevMonth" class="btn btn-light"><i class="fas fa-chevron-left"></i></button>
                        <button id="nextMonth" class="btn btn-light"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                {{-- Calendar --}}
                <div class="calendar-container mb-3">

                    <div class="calendar-days">
                        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
                        <div>Thu</div><div>Fri</div><div>Sat</div>
                    </div>
                    <div id="calendarDates" class="calendar-dates"></div>
                </div>

                <style>
                    .stats-card {
                        background: #fff;
                        border-radius: 15px;
                        padding: 20px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                        transition: all 0.3s ease;
                    }
                    .stats-card:hover { transform: translateY(-3px); }
                    .stats-header {
                        font-weight: 600; font-size: 1.1rem; color: #333;
                        border-bottom: 2px solid #f1f1f1;
                        padding-bottom: 8px; margin-bottom: 15px;
                        display: flex; align-items: center; gap: 8px;
                    }
                    .stats-body {
                        display: flex; justify-content: space-around; text-align: center;
                    }
                    .stats-item h4 { font-size: 2rem; font-weight: 700; margin-bottom: 5px; }
                    .text-present { color: #28a745; }
                    .text-absent { color: #dc3545; }
                    .text-working { color: #007bff; }
                    .text-warning { color: #ff9800; }
                    .text-info { color: #2196f3; }
                </style>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-header">
                                <i class="fas fa-user-check text-success"></i> Attendance Stats
                            </div>
                            <div class="stats-body">
                                <div class="stats-item">
                                    <h4 id="presentDays" class="text-present">0</h4>
                                    <p>Present</p>
                                </div>
                                <div class="stats-item">
                                    <h4 id="absentDays" class="text-absent">0</h4>
                                    <p>Absent</p>
                                </div>
                                <div class="stats-item">
                                    <h4 id="workingDays" class="text-working">0</h4>
                                    <p>Working Days</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="stats-card">
                            <div class="stats-header">
                                <i class="fas fa-calendar-check text-warning"></i> Leave Stats
                            </div>
                            <div class="stats-body flex-wrap">
                                <div class="stats-item">
                                    <h4 id="totalLeaves" class="text-primary">0</h4>
                                    <p>Total Leaves</p>
                                </div>
                                <div class="stats-item">
                                    <h4 id="usedLeaves" class="text-warning">0</h4>
                                    <p>Used Leaves</p>
                                </div>
                                <div class="stats-item">
                                    <h4 id="extraLeaves" class="text-success">0</h4>
                                    <p>Extra Leaves</p>
                                </div>
                                <div class="stats-item">
                                    <h4 id="remainingLeaves" class="text-info">0</h4>
                                    <p>Remaining Leaves</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Manual Attendance Modal --}}
<div class="modal fade" id="markAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark Attendance for <span id="selectedDateText"></span></h5>
            </div>
            <div class="modal-body">
                <p><strong>Check-in Time:</strong> <span id="checkinTime">-</span></p>
                <p><strong>Check-out Time:</strong> <span id="checkoutTime">-</span></p>
                <div class="mt-3">
                    <button id="markPresent" class="btn btn-success me-2">Mark Present</button>
                    <button id="markAbsent" class="btn btn-danger">Mark Absent</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toasts --}}
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
    <div id="toastContainer"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    let currentDate = new Date();
    let attendanceData = {};
    let selectedMarkDate = null;
    let currentYear = currentDate.getFullYear(); // ✅ track current loaded year

    // 🕒 Indian Time
    function updateIndianTime() {
        const options = { timeZone: "Asia/Kolkata", hour12: false };
        $("#currentTime").text(new Date().toLocaleString("en-IN", options));
    }
    setInterval(updateIndianTime, 1000);
    updateIndianTime();

    function showToast(type, msg) {
        const bg = type === 'success' ? 'bg-success' : 'bg-danger';
        const id = 'toast-' + Date.now();
        $('#toastContainer').append(`
            <div id="${id}" class="toast align-items-center text-white ${bg} border-0 mb-2" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${msg}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>`);
        new bootstrap.Toast($('#' + id)[0], { delay: 2500 }).show();
    }

    // ✅ Check Today’s Attendance
    function checkTodayStatus() {
        $.get("{{ route('attendance.status') }}", function (res) {
            if (res.status === 'found') {
                $('#checkInBtn').prop('disabled', !!res.checkin_time);
                $('#checkOutBtn').prop('disabled', !res.checkin_time || !!res.checkout_time);
            }
        });
    }

    function formatDate(date) {
        const y = date.getFullYear(), m = String(date.getMonth() + 1).padStart(2, '0'), d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function updateCalendar() {
        const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        $('.report-card-title').html(`<i class="fas fa-calendar me-2"></i>${months[currentDate.getMonth()]} ${currentDate.getFullYear()}`);

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const startDay = firstDay.getDay(), daysInMonth = lastDay.getDate();
        const todayFormatted = formatDate(new Date());

        $('#calendarDates').empty();
        for (let i = 0; i < startDay; i++) $('#calendarDates').append('<div class="calendar-date other-month"></div>');

        for (let i = 1; i <= daysInMonth; i++) {
            const d = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
            const formatted = formatDate(d);
            let status = (attendanceData[formatted] || '').toLowerCase();
            let cls = 'calendar-date';
            if (formatted === todayFormatted) cls += ' today';
            if (status === 'present') cls += ' present';
            if (status === 'absent') cls += ' absent';
            $('#calendarDates').append(`<div class="${cls}" data-date="${formatted}">${i}</div>`);
        }
    }

    // ✅ Load attendance for selected month
    function loadAttendanceEvents() {
        const month = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`;
        let req = { month: month };
        @if (isset($singleUserId))
            req.singleUserId = "{{ $singleUserId }}";
        @endif
        $.get("{{ route('attendance.events') }}", req, function (data) {
            attendanceData = {};
            data.forEach(e => attendanceData[e.start] = e.title);
            updateCalendar();
            updateStats();
        });
    }

    function updateStats() {
        const present = Object.values(attendanceData).filter(v => v.toLowerCase() === 'present').length;
        const absent = Object.values(attendanceData).filter(v => v.toLowerCase() === 'absent').length;
        const totalDays = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
        $('#presentDays').text(present);
        $('#absentDays').text(absent);
        $('#workingDays').text(totalDays);
    }

    // ✅ Load Leave Data for current year only when year changes
    function loadLeaveData() {
        const selectedYear = currentDate.getFullYear();
        if (selectedYear === currentYear && $('#totalLeaves').text() !== '0') return; // prevent reloading same year
        currentYear = selectedYear;

        const userId = "{{ isset($singleUserId) ? $singleUserId : Session::get('user')['id'] }}";
        $.get(`/check-leave-track/${userId}`, { year: selectedYear }, function (res) {
            if (res.status === 'success') {
                $('#totalLeaves').text(res.total_leaves);
                $('#usedLeaves').text(res.used_leaves);
                $('#extraLeaves').text(res.extra_leaves);
                $('#remainingLeaves').text(res.remaining_leaves);
            } else {
                $('#totalLeaves,#usedLeaves,#extraLeaves,#remainingLeaves').text('0');
            }
        });
    }

    // ✅ Month navigation (reload attendance + leave only if year changed)
    $('#prevMonth, #nextMonth').click(function () {
        const direction = this.id === 'prevMonth' ? -1 : 1;
        const prevYear = currentDate.getFullYear();
        currentDate.setMonth(currentDate.getMonth() + direction);
        updateCalendar();
        loadAttendanceEvents();
        if (currentDate.getFullYear() !== prevYear) loadLeaveData(); // ✅ only on year change
    });

    // ✅ Manual attendance (for Station Head)
    @if (isset($singleUserId))
    $(document).on('click', '.calendar-date', function () {
        const clicked = $(this).data('date');
        if (new Date(clicked) > new Date()) return;
        selectedMarkDate = clicked;
        $('#selectedDateText').text(clicked);

        $.get("{{ route('attendance.checkStatus') }}", { date: clicked, user_id: "{{ $singleUserId }}" }, function (res) {
            const modal = new bootstrap.Modal('#markAttendanceModal');
            modal.show();
            $('#checkinTime').text(res.checkin_time || '-');
            $('#checkoutTime').text(res.checkout_time || '-');
        });
    });

    $('#markPresent, #markAbsent').click(function () {
        const status = $(this).attr('id') === 'markPresent' ? 'Present' : 'Absent';
        $.post("{{ route('attendance.manualMark') }}", {
            _token: "{{ csrf_token() }}", date: selectedMarkDate, status: status,
            user_id: "{{ $singleUserId }}"
        }, function (res) {
            showToast(res.status, res.message);
            loadAttendanceEvents();
            loadLeaveData();
            bootstrap.Modal.getInstance(document.getElementById('markAttendanceModal')).hide();
        });
    });
    @endif

    $('#checkInBtn').click(function () {
        $.post("{{ route('attendance.checkin') }}", { _token: "{{ csrf_token() }}" }, function (res) {
            showToast(res.status, res.message);
            loadAttendanceEvents(); checkTodayStatus();
        });
    });

    $('#checkOutBtn').click(function () {
        $.post("{{ route('attendance.checkout') }}", { _token: "{{ csrf_token() }}" }, function (res) {
            showToast(res.status, res.message);
            loadAttendanceEvents(); checkTodayStatus();
        });
    });

    // ✅ Initial Load
    checkTodayStatus();
    loadAttendanceEvents();
    loadLeaveData();
});
</script>
@endsection
