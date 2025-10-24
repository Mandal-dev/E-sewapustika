@extends('Dashboard.header')

@section('data')
<style>
/* ------------------------
   GENERAL DASHBOARD LAYOUT
---------------------------*/
body, .dashboard-content {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
}

.app-content {
    margin: 0 20px;
}

/* ------------------------
   LIST LAYOUT
---------------------------*/
.list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* ------------------------
   CONTENT CARDS
---------------------------*/
.content-card {
    background: #fff;
    border-radius: 1rem;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    gap: 20px;
    transition: all 0.2s ease;
}

/* ------------------------
   CALENDAR
---------------------------*/
.report-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 10px;
    border-bottom: 1px solid #e3e6f0;
}

.report-card-title {
    font-size: 1.25rem;
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
.report-btn:hover { background: #e2e6ea; }

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-weight: 600;
    color: #135186;
    margin-bottom: 10px;
}

.calendar-dates {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
}

.calendar-date {
    text-align: center;
    padding: 10px 0;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
    position: relative;
}

.calendar-date.today {
    border: 2px solid #1cc88a;
    color: #1cc88a;
}

.calendar-date.selected {
    background-color: #135186;
    color: #fff;
}

.calendar-date.present {
    background-color: #d4edda;
    color: #155724;
}
.calendar-date.present::after {
    content: '✓';
    position: absolute;
    top: 2px;
    right: 4px;
    font-size: 0.7rem;
    font-weight: bold;
}

/* All days are working days */
.calendar-date.weekend { color: #135186; }

/* ------------------------
   ATTENDANCE STATS
---------------------------*/
.attendance-stats {
    display: flex;
    justify-content: space-around;
    border-top: 1px solid #e3e6f0;
    padding-top: 15px;
}

.stat-item { text-align: center; }
.stat-value { font-size: 1.8rem; font-weight: 700; color: #135186; }
.stat-label { font-size: 0.85rem; color: #6c757d; }

/* ------------------------
   BUTTONS
---------------------------*/
.view-btn, .btn-success, .btn-checkout {
    border-radius: 8px;
    font-weight: 500;
    padding: 10px 15px;
    transition: all 0.2s ease;
    text-align: center;
}

.view-btn:hover { background-color: #0f3c65; color: #fff; }
.btn-success:hover { background-color: #16713d; color: #fff; }
.btn-checkout { background: #f97316; color: #fff; }
.btn-checkout:hover { background: #ea580c; }

/* ------------------------
   ATTENDANCE STATUS
---------------------------*/
.attendance-status {
    padding: 12px 15px;
    border-radius: 8px;
    font-weight: 500;
    border-left: 4px solid;
    transition: all 0.2s ease;
}

.status-not-checked-in { background:#fdecea; color:#b71c1c; border-left-color:#e74a3b; }
.status-checked-in { background:#d4edda; color:#155724; border-left-color:#1cc88a; }

/* ------------------------
   RESPONSIVE
---------------------------*/
@media (max-width:768px){
    .calendar-date { padding: 8px 0; font-size: 0.9rem; }
    .stat-value { font-size: 1.5rem; }
}

/* ------------------------
   ACTIONS ROW
---------------------------*/
.actions-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}

.actions-row .view-btn,
.actions-row .btn,
.actions-row .date-input,
.actions-row .attendance-status {
    flex: 1 1 auto;
    min-width: 150px;
}

/* Mobile view: stack vertically */
@media (max-width: 768px) {
    .actions-row {
        flex-direction: column;
        align-items: stretch;
    }
    .actions-row .view-btn,
    .actions-row .btn,
    .actions-row .date-input,
    .actions-row .attendance-status {
        flex: 1 1 100%;
    }
}
</style>

<div class="app-content mt-4">
    <div class="dashboard-content">
        <h3 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Attendance Calendar</h3>

        <div class="list">

            <!-- Actions Card -->
            <div class="content-card">
                <h5 class="mb-2"><i class="fas fa-tasks me-2"></i>Actions</h5>

                <div class="actions-row">
                    <button id="checkInBtn" class="view-btn"><i class="fas fa-sign-in-alt me-2"></i>Check-in</button>

                    <input type="date" id="attendanceDate" class="form-control date-input" hidden4>

                    <button id="markAttendanceBtn" class="btn btn-success" disabled>
                        <i class="fas fa-check-circle me-2"></i>Mark Attendance
                    </button>

                    <button id="checkoutBtn" class="btn btn-checkout"><i class="fas fa-sign-out-alt me-2"></i>Check-out</button>

                    <div class="attendance-status status-not-checked-in">
                        <i class="fas fa-info-circle me-2"></i>Status: Not Checked In
                    </div>
                </div>

                <div id="message" class="mt-2"></div>
            </div>

            <!-- Calendar Card -->
            <div class="content-card">
                <div class="report-card-header mb-4">
                    <div class="report-card-title"><i class="fas fa-calendar me-2"></i>Calendar</div>
                    <div>
                        <button id="prevMonth" class="report-btn"><i class="fas fa-chevron-left"></i></button>
                        <button id="nextMonth" class="report-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>

                <div class="calendar-container">
                    <div class="calendar-days">
                        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
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
$(document).ready(function(){
    let checkedIn = false;
    let currentDate = new Date();
    let selectedDate = new Date();
    let attendanceData = {};

    $('#attendanceDate').val(formatDate(selectedDate));
    updateCalendar();
    loadAttendanceEvents();

    $('#prevMonth').click(function(){
        currentDate.setMonth(currentDate.getMonth() - 1);
        updateCalendar();
        loadAttendanceEvents();
    });

    $('#nextMonth').click(function(){
        currentDate.setMonth(currentDate.getMonth() + 1);
        updateCalendar();
        loadAttendanceEvents();
    });

    // Check-in
    $('#checkInBtn').click(function(){
        let date = $('#attendanceDate').val();
        $.post("{{ route('attendance.checkin') }}", {_token: "{{ csrf_token() }}", date: date}, function(response){
            if(response.status === 'success'){
                checkedIn = true;
                $('#markAttendanceBtn').prop('disabled', false);
                $('.attendance-status')
                    .removeClass('status-not-checked-in')
                    .addClass('status-checked-in')
                    .html('<i class="fas fa-check-circle me-2"></i>Status: Checked In');
                $('#message').html('<div class="alert alert-success mt-2">'+response.message+'</div>');
                loadAttendanceEvents();
            } else {
                $('#message').html('<div class="alert alert-danger mt-2">'+response.message+'</div>');
            }
        });
    });

    // Mark attendance (for current day)
    $('#markAttendanceBtn').click(function(){
        let date = $('#attendanceDate').val();
        $.post("{{ route('attendance.mark') }}", {_token: "{{ csrf_token() }}", attendance_date: date}, function(response){
            if(response.status === 'success'){
                $('#message').html('<div class="alert alert-success mt-3">'+response.message+'</div>');
                attendanceData[date] = 'Present';
                updateCalendar();
                updateStats();
            } else {
                $('#message').html('<div class="alert alert-danger mt-3">'+response.message+'</div>');
            }
        });
    });

    // Check-out
    $('#checkoutBtn').click(function(){
        let date = $('#attendanceDate').val();
        $.post("{{ route('attendance.checkout') }}", {_token: "{{ csrf_token() }}", date: date}, function(response){
            if(response.status==='success'){
                $('#message').html('<div class="alert alert-success mt-3">'+response.message+'</div>');
                $('.attendance-status')
                    .removeClass('status-checked-in')
                    .addClass('status-not-checked-in')
                    .html('<i class="fas fa-info-circle me-2"></i>Status: Not Checked In');
                $('#markAttendanceBtn').prop('disabled', true);
                checkedIn = false;
                loadAttendanceEvents();
            } else {
                $('#message').html('<div class="alert alert-danger mt-3">'+response.message+'</div>');
            }
        });
    });

    // Load attendance events
    function loadAttendanceEvents(){
        const month = currentDate.getFullYear() + '-' + String(currentDate.getMonth()+1).padStart(2,'0');
        $.get("{{ route('attendance.events') }}", {month: month}, function(events){
            attendanceData = {};
            events.forEach(event => {
                attendanceData[event.start] = event.title;
            });
            updateCalendar();
            updateStats();
        });
    }

    function updateCalendar(){
        const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
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
            if(dateFormatted===todayFormatted) classes += ' today';
            if(dateFormatted===selectedFormatted) classes += ' selected';
            if(attendanceData[dateFormatted]==='Present') classes += ' present';

            $('#calendarDates').append(`<div class="${classes}" data-date="${dateFormatted}">${i}</div>`);
        }

        $('.calendar-date').click(function(){
            const clickedDate = $(this).data('date');
            if(clickedDate && !$(this).hasClass('other-month')){
                selectedDate = new Date(clickedDate+'T00:00:00');
                $('#attendanceDate').val(clickedDate);
                updateCalendar();
            }
        });
    }

    function formatDate(date){
        const year=date.getFullYear();
        const month=String(date.getMonth()+1).padStart(2,'0');
        const day=String(date.getDate()).padStart(2,'0');
        return `${year}-${month}-${day}`;
    }

    function updateStats(){
        const presentCount = Object.values(attendanceData).filter(v => v==='Present').length;
        $('#presentDays').text(presentCount);

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const workingDays = lastDay.getDate(); // All days are working days
        $('#workingDays').text(workingDays);
        $('#absentDays').text(Math.max(0, workingDays - presentCount));
    }
});
</script>
@endsection
