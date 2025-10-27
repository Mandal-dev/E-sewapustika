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

<div class="app-content">
<!-- Flash Messages -->
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="background-color:#d4edda; color:#155724; font-weight:500;">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert" style="background-color:#f8d7da; color:#721c24; font-weight:500;">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


    <!-- Mark Attendance Button -->
    @if (in_array($designation, ['Admin', 'Station_Head', 'Head_Person','Police']))
        <div class="mb-3">
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">Mark Attendance</a>
        </div>
    @endif

    <!-- Table Section -->
    <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">Police Attendance Records</h5>

            <div class="search-container position-relative" style="width: 300px;">
                <input type="text" id="searchInput" class="form-control ps-4" placeholder="Search attendance...">
                <i class="fas fa-search search-icon position-absolute"></i>
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

                            <td>{{ $att->status }}</td>
                            <td>{{ \Carbon\Carbon::parse($att->attendance_date)->format('d-m-Y') }}</td>
                            <td>button view</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Optional: JS for live search -->
<script>
    $(document).ready(function(){
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection
