@extends('Dashboard.header')

@section('data')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/calender.css') }}">

<div class="app-content mt-4">
    <div class="dashboard-content">
        <h3 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Attendance Calendar</h3>

        <div class="list">
            <div class="content-card">
                <div class="report-card-header mb-3 d-flex justify-content-between align-items-center">
                    <div class="report-card-title"><i class="fas fa-calendar me-2"></i>Calendar</div>
                    <div class="d-flex align-items-center gap-2">
                        <button id="prevMonth" class="report-btn btn btn-light"><i class="fas fa-chevron-left"></i></button>
                        <button id="nextMonth" class="report-btn btn btn-light"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <div class="actions-row mb-2">
                    {{-- ✅ Only show for logged-in user (not when viewing single user) --}}
                    @if (!isset($singleUserId))
                        <button id="checkInBtn" class="view-btn btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Mark Today Attendance
                        </button>
                    @endif
                    <input type="date" id="attendanceDate" class="form-control date-input" hidden>
                    <div id="message" class="mt-2"></div>
                </div>

                {{-- Calendar --}}
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

                {{-- Stats --}}
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

<!-- Modal for marking attendance -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-labelledby="markAttendanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markAttendanceLabel">Mark Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-semibold mb-3">Select attendance status for <span id="selectedDateText"></span>:</p>
                <button id="markPresent" class="btn btn-success me-2">Mark Present</button>
                <button id="markAbsent" class="btn btn-danger">Mark Absent</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    let currentDate = new Date();
    let selectedDate = new Date();
    let attendanceData = {};
    let selectedMarkDate = null;

    $('#attendanceDate').val(formatDate(selectedDate));
    updateCalendar();
    loadAttendanceEvents();

    // --------------------------
    // Navigation between months
    // --------------------------
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

    // --------------------------
    // Show message
    // --------------------------
    function showMessage(type, text) {
        const alertType = type === 'success' ? 'alert-success' : 'alert-danger';
        $('#message').html(`<div class="alert ${alertType}" role="alert">${text}</div>`);
        setTimeout(() => {
            $('#message').fadeOut(300, function() {
                $(this).html('').show();
            });
        }, 4000);
    }

    // --------------------------
    // For Station Head - modal marking
    // --------------------------
    @if (isset($singleUserId))
    $(document).on('click', '.calendar-date', function () {
        const clickedDate = $(this).data('date');
        const today = new Date();
        const selected = new Date(clickedDate);
        if (selected > today) return; // prevent future marking
        selectedMarkDate = clickedDate;
        $('#selectedDateText').text(clickedDate);

        $.ajax({
            url: "{{ route('attendance.checkStatus') }}",
            type: "GET",
            data: {
                date: clickedDate,
                user_id: "{{ $singleUserId }}"
            },
            success: function (response) {
                const modal = new bootstrap.Modal(document.getElementById('markAttendanceModal'));
                modal.show();

                // Reset buttons
                $('#markPresent').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                $('#markAbsent').prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger');

                // Disable based on current status
                if (response.status === 'marked') {
                    if (response.attendance_status === 'Present') {
                        $('#markPresent').prop('disabled', true)
                            .removeClass('btn-success').addClass('btn-secondary');
                    } else if (response.attendance_status === 'Absent') {
                        $('#markAbsent').prop('disabled', true)
                            .removeClass('btn-danger').addClass('btn-secondary');
                    }
                }
            },
            error: function () {
                alert('Failed to fetch attendance status. Try again.');
            }
        });
    });

    // Mark present/absent
    $('#markPresent, #markAbsent').click(function () {
        const status = $(this).attr('id') === 'markPresent' ? 'Present' : 'Absent';
        markAttendance(selectedMarkDate, status);
        bootstrap.Modal.getInstance(document.getElementById('markAttendanceModal')).hide();
    });
    @endif

    // --------------------------
    // For logged-in police (self-mark)
    // --------------------------
    $('#checkInBtn').click(function() {
        let date = $('#attendanceDate').val();
        markAttendance(date, 'Present');
    });

    // --------------------------
    // AJAX: Mark Attendance
    // --------------------------
    function markAttendance(date, status) {
        @if (!isset($singleUserId))
            $.post("{{ route('attendance.manualMark') }}", {
                _token: "{{ csrf_token() }}",
                date: date,
                status: status
            }, function(response) {
                if (response.status === 'success') {
                    showMessage('success', response.message);
                    loadAttendanceEvents();
                } else {
                    showMessage('error', response.message);
                }
            });
        @else
            $.post("{{ route('attendance.manualMark') }}", {
                _token: "{{ csrf_token() }}",
                date: date,
                status: status,
                user_id: "{{ $singleUserId }}"
            }, function(response) {
                if (response.status === 'success') {
                    showMessage('success', response.message);
                    loadAttendanceEvents();
                } else {
                    showMessage('error', response.message);
                }
            });
        @endif
    }

    // --------------------------
    // Load attendance data
    // --------------------------
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

    // --------------------------
    // Render calendar
    // --------------------------
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
        for (let i = 1; i <= daysInMonth; i++) {
            const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
            const dateFormatted = formatDate(date);

            let status = (attendanceData[dateFormatted] || '').toLowerCase();
            let classes = 'calendar-date';
            if (dateFormatted === todayFormatted) classes += ' today';
            if (status === 'present') classes += ' present';
            else if (status === 'absent') classes += ' absent';

            $('#calendarDates').append(`<div class="${classes}" data-date="${dateFormatted}">${i}</div>`);
        }
    }

    // --------------------------
    // Utility: format date
    // --------------------------
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // --------------------------
    // Stats count
    // --------------------------
    function updateStats() {
        const presentCount = Object.values(attendanceData).filter(v => v.toLowerCase() === 'present').length;
        const absentCount = Object.values(attendanceData).filter(v => v.toLowerCase() === 'absent').length;
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const workingDays = lastDay.getDate();
        $('#presentDays').text(presentCount);
        $('#absentDays').text(absentCount);
        $('#workingDays').text(workingDays);
    }
});
</script>
@endsection
