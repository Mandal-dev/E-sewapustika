@extends('Dashboard.header')

@section('data')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ---------------------------------------
   GENERAL DASHBOARD LAYOUT
----------------------------------------*/
body,
.dashboard-content {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
}

.app-content {
    margin: 0 20px;
}

h3 {
    font-weight: 600;
    color: #135186;
}

/* ---------------------------------------
   CONTENT CARDS
----------------------------------------*/
.content-card {
    background: #fff;
    border-radius: 1rem;
    padding: 20px 25px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    gap: 20px;
    transition: all 0.2s ease-in-out;
}

/* ---------------------------------------
   CARD HEADER & BUTTONS
----------------------------------------*/
.report-card-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    border-bottom: 1px solid #e3e6f0;
    gap: 10px;
}

.report-card-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #135186;
}

.report-btn {
    background: #f3f4f6;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.2s;
}

.report-btn:hover {
    background: #e2e6ea;
}

/* ---------------------------------------
   CALENDAR STYLING
----------------------------------------*/
.calendar-container {
    margin-top: 10px;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-weight: 600;
    color: #135186;
    margin-bottom: 10px;
    font-size: 0.9rem;
}

.calendar-dates {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-date {
    text-align: center;
    padding: 14px 0;
    border-radius: 8px;
    font-weight: 500;
    cursor: default;
    transition: all 0.2s ease;
    font-size: 0.95rem;
    background-color: #fdfdfd;
    border: 1px solid #e9ecef;
}

.calendar-date:hover {
    background-color: #eef5fb;
}

.calendar-date.today {
    border: 2px solid #1cc88a;
    color: #1cc88a;
    font-weight: 600;
}

.calendar-date.selected {
    background-color: #135186;
    color: #fff;
}

.calendar-date.present {
    background-color: #d4edda;
    color: #155724;
    position: relative;
}

.calendar-date.present::after {
    content: '✓';
    position: absolute;
    top: 4px;
    right: 6px;
    font-size: 0.7rem;
    font-weight: bold;
}

/* ---------------------------------------
   ATTENDANCE STATS
----------------------------------------*/
.attendance-stats {
    display: flex;
    justify-content: space-around;
    border-top: 1px solid #e3e6f0;
    padding-top: 15px;
    gap: 10px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #135186;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
}

/* ---------------------------------------
   BUTTONS & ACTIONS
----------------------------------------*/
.view-btn {
    background-color: #135186;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 0.9rem;
    font-weight: 500;
    line-height: 1.2;
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.1s ease;
}

.view-btn:hover {
    background-color: #0d3c65;
    transform: scale(1.02);
}

.actions-row {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

/* ---------------------------------------
   STATUS MESSAGES
----------------------------------------*/
#message .alert {
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 0.95rem;
    margin-top: 10px;
}

/* ---------------------------------------
   RESPONSIVE DESIGN
----------------------------------------*/
@media (max-width: 992px) {
    .calendar-date {
        padding: 12px 0;
        font-size: 0.9rem;
    }
    .stat-value {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .content-card {
        padding: 15px;
    }

    .calendar-date {
        padding: 10px 0;
        font-size: 0.85rem;
    }

    .attendance-stats {
        flex-direction: column;
        gap: 12px;
    }

    .actions-row {
        flex-direction: column;
        align-items: stretch;
    }

    .view-btn,
    .report-btn,
    .form-control {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .calendar-days div {
        font-size: 0.8rem;
    }

    .calendar-date {
        font-size: 0.8rem;
        padding: 8px 0;
    }

    .stat-value {
        font-size: 1.3rem;
    }

    .stat-label {
        font-size: 0.8rem;
    }
}
</style>

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
                    <button id="checkInBtn" class="view-btn"><i class="fas fa-sign-in-alt me-2"></i>Mark Attendance</button>
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
            let checkedIn = false;
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
                        checkedIn = true;
                        $('.attendance-status')
                            .removeClass('status-not-checked-in')
                            .addClass('status-checked-in')
                            .html('<i class="fas fa-check-circle me-2"></i>Status: Checked In');

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
                $.get("{{ route('attendance.events') }}", {
                    month: month
                }, function(events) {
                    attendanceData = {};
                    events.forEach(event => {
                        attendanceData[event.start] = event.title;
                    });
                    updateCalendar();
                    updateStats();
                });
            }

            function updateCalendar() {
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August",
                    "September", "October", "November", "December"
                ];
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
