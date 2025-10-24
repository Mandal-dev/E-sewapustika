@extends('layouts.app')

@section('data')
<div class="container">
    <h2>Police Attendance</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        @php
            $user = session('user');
        @endphp

        @if($user && in_array($user['designation_type'], ['Admin', 'Station_Head', 'Head_Person']))
            <a href="{{ route('attendance.create') }}" class="btn btn-primary">Mark Attendance</a>
        @endif
    </div>

    <div id="attendance-table">
        @include('attendance.table')
    </div>
</div>
@endsection
