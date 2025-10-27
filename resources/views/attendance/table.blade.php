@extends('Dashboard.header')

@section('data')
<!-- Bootstrap + Custom CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@php
    $designation = Session::get('user.designation_type');
@endphp

<div class="app-content mt-4">

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert"
             style="background-color:#d4edda; color:#155724; font-weight:500;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert"
             style="background-color:#f8d7da; color:#721c24; font-weight:500;">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Mark Attendance Button -->
    @if (in_array($designation, ['Admin', 'Station_Head', 'Head_Person', 'Police']))
        <div class="mb-3">
            <a href="{{ route('attendance.create') }}" class="btn btn-primary shadow-sm px-4 py-2 fw-semibold">
                <i class="fas fa-sign-in-alt me-2"></i>Mark Attendance
            </a>
        </div>
    @endif


<!-- ======================
     ATTENDANCE SUMMARY
====================== -->
<style>
.attendance-card-container {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    justify-content: space-between;
}

.attendance-btn {
    flex: 1;
    min-width: 200px;
    text-align: center;
    padding: 18px 10px;
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    background-color: #fff;
}

.attendance-btn:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.attendance-all {
    background-color: #e3f2fd;
    color: #0d6efd;
}
.attendance-present {
    background-color: #d4edda;
    color: #198754;
}
.attendance-absent {
    background-color: #f8d7da;
    color: #dc3545;
}
.attendance-leave {
    background-color: #fff3cd;
    color: #856404;
}

.attendance-btn h6 {
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 16px;
}

.attendance-btn h4 {
    font-weight: 700;
    margin: 0;
    font-size: 26px;
}
</style>

<div class="attendance-card-container mb-4">
    <button class="attendance-btn attendance-all" onclick="filterAttendance('All')">
        <h6>All</h6>
        <h4>{{ $attendance->count() }}</h4>
    </button>

    <button class="attendance-btn attendance-present" onclick="filterAttendance('Present')">
        <h6>Present</h6>
        <h4>{{ $attendance->where('status', 'Present')->count() }}</h4>
    </button>

    <button class="attendance-btn attendance-absent" onclick="filterAttendance('Absent')">
        <h6>Absent</h6>
        <h4>{{ $attendance->where('status', 'Absent')->count() }}</h4>
    </button>

    <button class="attendance-btn attendance-leave" onclick="filterAttendance('Leave')">
        <h6>Leave</h6>
        <h4>{{ $attendance->where('status', 'Leave')->count() }}</h4>
    </button>
</div>


<!-- ======================
     ATTENDANCE TABLE
====================== -->
<div class="table-section p-3" style="background: #fff; border-radius: 8px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0">Police Attendance Records</h5>

        <div class="search-container position-relative" style="width: 300px;">
            <input type="text" id="searchInput" class="form-control ps-4" placeholder="Search attendance...">
            <i class="fas fa-search search-icon position-absolute"
               style="right:15px; top:50%; transform:translateY(-50%); color:#aaa;"></i>
        </div>
    </div>

    <div class="table-responsive" style="max-height:400px; overflow-y:auto; padding:10px;">
        <table class="table table-bordered align-middle my-rounded-table">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Police Name</th>
                    <th>Station</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendance as $index => $att)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $att->police_name ?? '-' }}</td>
                        <td>{{ $att->station_name ?? '-' }}</td>
                        <td>
                            @if($att->status == 'Present')
                                <span class="badge bg-success">{{ $att->status }}</span>
                            @elseif($att->status == 'Absent')
                                <span class="badge bg-danger">{{ $att->status }}</span>
                            @elseif($att->status == 'Leave')
                                <span class="badge bg-warning text-dark">{{ $att->status }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $att->status }}</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($att->attendance_date)->format('d-m-Y') }}</td>
                        <td>
                            <a href="{{ route('attendance.show', $att->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

<!-- ======================
     SCRIPT SECTION
====================== -->
<script>
$(document).ready(function(){
    // Live Search
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Filter Attendance
function filterAttendance(status) {
    if (status === 'All') {
        $("table tbody tr").show();
    } else {
        $("table tbody tr").each(function() {
            $(this).toggle($(this).find("td:eq(3)").text().trim() === status);
        });
    }
}
</script>
@endsection
