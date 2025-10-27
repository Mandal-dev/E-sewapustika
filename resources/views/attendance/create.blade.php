@extends('Dashboard.header')

@section('data')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/calender.css') }}">

    <style>
        /* ------------------------
           CALENDAR STYLING FIXES
        ---------------------------*/
        .calendar-container {
            margin-top: 20px;
            border-radius: 12px;
            background: #fff;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .calendar-days, .calendar-dates {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
        }

        .calendar-days div {
            font-weight: 600;
            color: #555;
            padding: 10px 0;
        }

        .calendar-date {
            padding: 12px 0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s ease;
        }

        .calendar-date:hover {
            background-color: #f1f1f1;
        }

        .calendar-date.other-month {
            background-color: transparent;
            cursor: default;
        }

        .calendar-date.today {
            border: 2px solid #007bff !important;
            font-weight: bold;
        }

        .calendar-date.present {
            background-color: #D4EDDA !important; /* ✅ Green */
            color: green !important;
        }

        .calendar-date.absent {
            background-color: #eda5ac !important; /* ✅ Red */
            color: red !important;
        }

        .attendance-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            gap: 10px;
        }

        .stat-item {
            flex: 1;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stat-label {
            color: #555;
        }
    </style>

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
                        @if (!isset($singleUserId))
                            <button id="checkInBtn" class="view-btn btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Mark Today Attendance
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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="markAttendanceModal" tabindex="-1" aria-labelledby="markAttendanceLabel"
        aria-hidden="true">
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

            // ✅ Only allow modal marking if singleUserId exists
            @if (isset($singleUserId))
                $(document).on('click', '.calendar-date', function() {
                    const clickedDate = $(this).data('date');
                    const today = new Date();
                    const selected = new Date(clickedDate);
                    if (selected > today) return; // prevent future marking
                    selectedMarkDate = clickedDate;
                    $('#selectedDateText').text(clickedDate);
                    new bootstrap.Modal(document.getElementById('markAttendanceModal')).show();
                });

                $('#markPresent, #markAbsent').click(function() {
                    const status = $(this).attr('id') === 'markPresent' ? 'Present' : 'Absent';
                    markAttendance(selectedMarkDate, status);
                    bootstrap.Modal.getInstance(document.getElementById('markAttendanceModal')).hide();
                });
            @endif

            // ✅ Direct check-in for logged-in user (no popup)
            $('#checkInBtn').click(function() {
                let date = $('#attendanceDate').val();
                markAttendance(date, 'Present');
            });

            // ✅ AJAX Call to mark attendance
            function markAttendance(date, status) {
                @if (!isset($singleUserId))
                    if (status === 'Present') {
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
                    }
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

            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

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
