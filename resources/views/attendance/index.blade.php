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

<div class="app-content mt-4">
    @php
        $designation = Session::get('user')['designation_type'] ?? null;
    @endphp

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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

    <!-- Attendance Table -->
    <div class="table-section p-3" style="background:#fff; border-radius:8px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Police Attendance Records</h5>

            <div class="d-flex" style="width:300px;">
                <input type="text" id="searchInput" class="form-control me-2"
                       placeholder="Search attendance..." autocomplete="off">
                <button type="button" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Table container for AJAX content -->
        <div id="attendanceTableContainer">
            @include('attendance.table', ['attendance' => $attendance])
        </div>
    </div>
</div>

<script>
    // Debounce function: delays execution until user stops typing
    function debounce(func, delay) {
        let timeout;
        return function () {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Perform AJAX search
    function performSearch() {
        let search = $('#searchInput').val();

        $.ajax({
            url: "{{ route('attendance.search') }}",
            method: 'GET',
            data: { search: search },
            beforeSend: function () {
                $('#attendanceTableContainer').html('<div class="text-center py-3 text-muted">Loading...</div>');
            },
            success: function (data) {
                $('#attendanceTableContainer').html(data);
            },
            error: function () {
                $('#attendanceTableContainer').html('<div class="text-center text-danger py-3">Error loading data.</div>');
            }
        });
    }

    // Attach keyup listener with debounce (50 ms)
    $(document).on('keyup', '#searchInput', debounce(performSearch, 500));

    // Handle pagination via AJAX
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let pageUrl = $(this).attr('href');
        $.ajax({
            url: pageUrl,
            success: function (data) {
                $('#attendanceTableContainer').html(data);
            }
        });
    });
</script>
@endsection
