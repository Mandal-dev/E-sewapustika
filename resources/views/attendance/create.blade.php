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
                    <div class="attendance-stats mt-4 d-flex justify-content-around text-center">
                        <div>
                            <h4 id="presentDays" class="text-success fw-bold">0</h4>
                            <p>Present</p>
                        </div>
                        <div>
                            <h4 id="absentDays" class="text-danger fw-bold">0</h4>
                            <p>Absent</p>
                        </div>
                        <div>
                            <h4 id="workingDays" class="text-primary fw-bold">0</h4>
                            <p>Working Days</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- Manual Attendance Modal (Station Head) --}}
<!-- Attendance Modal -->
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


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Toast Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
        <div id="toastContainer"></div>
    </div>

    <script>
        $(document).ready(function() {
            let currentDate = new Date();
            let attendanceData = {};
            let selectedMarkDate = null;

            // ✅ Indian Time
            function updateIndianTime() {
                const options = {
                    timeZone: "Asia/Kolkata",
                    hour12: false
                };
                const now = new Date().toLocaleString("en-IN", options);
                $("#currentTime").text(now);
            }
            setInterval(updateIndianTime, 1000);
            updateIndianTime();

            // ✅ Toast Message (Replaces alert boxes)
            function showToast(type, message) {
                const bgClass = type === 'success' ? 'bg-success text-white' : 'bg-danger text-white';
                const icon = type === 'success' ? '✅' : '⚠️';

                const toastId = 'toast-' + Date.now();
                const toastHtml = `
                <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">${icon} ${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>`;

                $('#toastContainer').append(toastHtml);
                const toastEl = new bootstrap.Toast($('#' + toastId)[0], {
                    delay: 3000
                });
                toastEl.show();
            }

            // ✅ Check Today’s Check-In / Check-Out Status
            function checkTodayStatus() {
                $.get("{{ route('attendance.status') }}", function(response) {
                    if (response.status === 'found') {
                        const checkin = response.checkin_time;
                        const checkout = response.checkout_time;

                        if (!checkin && !checkout) {
                            $('#checkInBtn').prop('disabled', false);
                            $('#checkOutBtn').prop('disabled', true);
                        } else if (checkin && !checkout) {
                            $('#checkInBtn').prop('disabled', true);
                            $('#checkOutBtn').prop('disabled', false);
                        } else {
                            $('#checkInBtn').prop('disabled', true);
                            $('#checkOutBtn').prop('disabled', true);
                        }
                    } else {
                        $('#checkInBtn').prop('disabled', false);
                        $('#checkOutBtn').prop('disabled', true);
                    }
                }).fail(() => showToast('error', 'Failed to check today’s attendance status.'));
            }

            // ✅ Month Navigation
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

            // ✅ Check-In
            $('#checkInBtn').click(function() {
                $.post("{{ route('attendance.checkin') }}", {
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    showToast(response.status, response.message);
                    loadAttendanceEvents();
                    checkTodayStatus();
                }).fail(() => showToast('error', 'Something went wrong during Check-In.'));
            });

            // ✅ Check-Out
            $('#checkOutBtn').click(function() {
                $.post("{{ route('attendance.checkout') }}", {
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    showToast(response.status, response.message);
                    loadAttendanceEvents();
                    checkTodayStatus();
                }).fail(() => showToast('error', 'Something went wrong during Check-Out.'));
            });

@if (isset($singleUserId))
$(document).on('click', '.calendar-date', function() {
    const clickedDate = $(this).data('date');
    if (new Date(clickedDate) > new Date()) return;
    selectedMarkDate = clickedDate;
    $('#selectedDateText').text(clickedDate);

    $.get("{{ route('attendance.checkStatus') }}", {
        date: clickedDate,
        user_id: "{{ $singleUserId }}"
    }, function(response) {
        const modal = new bootstrap.Modal('#markAttendanceModal');
        modal.show();

        // Reset display
        $('#checkinTime').text('-');
        $('#checkoutTime').text('-');

        // Show fetched times if available
        if (response.checkin_time) $('#checkinTime').text(response.checkin_time);
        if (response.checkout_time) $('#checkoutTime').text(response.checkout_time);

        $('#markPresent, #markAbsent').prop('disabled', false).removeClass('btn-secondary');

        if (response.status === 'marked') {
            if (response.attendance_status === 'Present')
                $('#markPresent').prop('disabled', true).addClass('btn-secondary');
            if (response.attendance_status === 'Absent')
                $('#markAbsent').prop('disabled', true).addClass('btn-secondary');
        }
    }).fail(() => showToast('error', 'Unable to check attendance status.'));
});

$('#markPresent, #markAbsent').click(function() {
    const status = $(this).attr('id') === 'markPresent' ? 'Present' : 'Absent';
    $.post("{{ route('attendance.manualMark') }}", {
        _token: "{{ csrf_token() }}",
        date: selectedMarkDate,
        status: status,
        user_id: "{{ $singleUserId }}"
    }, function(response) {
        showToast(response.status, response.message);
        loadAttendanceEvents();
        bootstrap.Modal.getInstance(document.getElementById('markAttendanceModal')).hide();
    }).fail(() => showToast('error', 'Failed to mark attendance.'));
});
@endif


            // ✅ Load Attendance
            function loadAttendanceEvents() {
                const month = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}`;
                let requestData = {
                    month: month
                };
                @if (isset($singleUserId))
                    requestData.singleUserId = "{{ $singleUserId }}";
                @endif

                $.get("{{ route('attendance.events') }}", requestData, function(events) {
                    attendanceData = {};
                    events.forEach(event => attendanceData[event.start] = event.title);
                    updateCalendar();
                    updateStats();
                }).fail(() => showToast('error', 'Failed to load attendance data.'));
            }

            // ✅ Render Calendar
            function updateCalendar() {
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ];
                $('.report-card-title').html('<i class="fas fa-calendar me-2"></i>' + monthNames[currentDate
                    .getMonth()] + ' ' + currentDate.getFullYear());

                const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                const startDay = firstDay.getDay();
                const daysInMonth = lastDay.getDate();

                $('#calendarDates').empty();
                for (let i = 0; i < startDay; i++)
                    $('#calendarDates').append('<div class="calendar-date other-month"></div>');

                const todayFormatted = formatDate(new Date());
                for (let i = 1; i <= daysInMonth; i++) {
                    const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
                    const dateFormatted = formatDate(date);
                    let status = (attendanceData[dateFormatted] || '').toLowerCase();
                    let cls = 'calendar-date';
                    if (dateFormatted === todayFormatted) cls += ' today';
                    if (status === 'present') cls += ' present';
                    if (status === 'absent') cls += ' absent';
                    $('#calendarDates').append(`<div class="${cls}" data-date="${dateFormatted}">${i}</div>`);
                }
            }

            // ✅ Helpers
            function formatDate(date) {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            function updateStats() {
                const present = Object.values(attendanceData).filter(v => v.toLowerCase() === 'present').length;
                const absent = Object.values(attendanceData).filter(v => v.toLowerCase() === 'absent').length;
                const totalDays = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
                $('#presentDays').text(present);
                $('#absentDays').text(absent);
                $('#workingDays').text(totalDays);
            }

            // ✅ Initial Load
            checkTodayStatus();
            loadAttendanceEvents();
        });
    </script>
@endsection
