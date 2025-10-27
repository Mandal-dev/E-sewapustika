@extends('Dashboard.header')

@section('data')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/calender.css') }}">


<div class="app-content mt-4">
    <div class="dashboard-content">
        <h3 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Attendance Calendar</h3>

        <div class="list">
            <div class="content-card">
                <div class="report-card-header mb-3">
                    <div class="report-card-title"><i class="fas fa-calendar me-2"></i>Calendar</div>

                    <div class="d-flex align-items-center gap-2">
                        <button id="prevMonth" class="report-btn"><i class="fas fa-chevron-left"></i></button>
                        <button id="nextMonth" class="report-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <div class="actions-row mb-2">
                    @if (!isset($singleUserId))
                        <button id="checkInBtn" class="view-btn">
                            <i class="fas fa-sign-in-alt me-2"></i>Mark Attendance
                        </button>
                    @endif
                    <input type="date" id="attendanceDate" class="form-control date-input" hidden>
                    <div id="message" class="mt-2"></div>
                </div>

                <div class="calendar-container">
                    <div class="calendar-days">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>
                    <div id="calendarDates" class="calendar-dates"></div>
                </div>

                <div class="attendance-stats">
                    <div class="stat-item">
                        <div class="stat-value" id="presentDays">0</div>
                        <div class="stat-label">Present</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="absentDays">0</div>
                        <div class="stat-label">Absent</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value" id="workingDays">0</div>
                        <div class="stat-label">Working Days</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let currentDate = new Date();
    let selectedDate = new Date();
    let attendanceData = {};

    $('#attendanceDate').val(formatDate(selectedDate));
    updateCalendar();
    loadAttendanceEvents();

    $('#prevMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
        loadAttendanceEvents();
    });

    $('#nextMonth').click(function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
        loadAttendanceEvents();
    });

    function showMessage(type, text) {
        const alertType = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#message').html(`<div class="alert ${alertType}" role="alert">${text}</div>`);
        setTimeout(() => {
            $('#message').fadeOut(300, function() {
                $(this).html('').show();
            });
        }, 4000);
    }

    // Check-in
    $('#checkInBtn').click(function() {
        let date = $('#attendanceDate').val();
        $.post("{{ route('attendance.checkin') }}", {
            _token: "{{ csrf_token() }}",
            date: date
        }, function(response) {
            if (response.status === 'success') {
                showMessage('success', response.message);
                loadAttendanceEvents();
            } else {
                showMessage('error', response.message);
            }
        });
    });

    // Load attendance events
    function loadAttendanceEvents() {
        const month = currentDate.getFullYear() + '-' + String(currentDate.getMonth() + 1).padStart(2, '0');
        let requestData = { month: month };

        @if (isset($singleUserId))
            requestData.singleUserId = "{{ $singleUserId }}";
        @endif

        $.get("{{ route('attendance.events') }}", requestData, function(events) {
            attendanceData = {};
            events.forEach(event => {
                attendanceData[event.start] = event.title;
            });
            updateCalendar();
            updateStats();
        });
    }

    function updateCalendar() {
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"];
        const headerText = monthNames[currentDate.getMonth()] + ' ' + currentDate.getFullYear();
        $('.report-card-title').html('<i class="fas fa-calendar me-2"></i>' + headerText);

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDay = firstDay.getDay();

        $('#calendarDates').empty();

        for (let i = 0; i < startingDay; i++) {
            $('#calendarDates').append('<div class="calendar-date other-month"></div>');
        }

        const todayFormatted = formatDate(new Date());
        const selectedFormatted = formatDate(selectedDate);

        for (let i = 1; i <= daysInMonth; i++) {
            const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
            const dateFormatted = formatDate(date);

            let classes = 'calendar-date';
            if (dateFormatted === todayFormatted) classes += ' today';
            if (dateFormatted === selectedFormatted) classes += ' selected';
            if (attendanceData[dateFormatted] === 'Present') classes += ' present';

            $('#calendarDates').append(`<div class="${classes}" data-date="${dateFormatted}">${i}</div>`);
        }
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function updateStats() {
        const presentCount = Object.values(attendanceData).filter(v => v === 'Present').length;
        $('#presentDays').text(presentCount);

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const workingDays = lastDay.getDate();
        $('#workingDays').text(workingDays);
        $('#absentDays').text(Math.max(0, workingDays - presentCount));
    }
});
</script>
@endsection
