    <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
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
            @forelse ($attendance as $index => $att)
                <tr>
                    <td>{{ $attendance->firstItem() + $index }}</td>
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
                            <span class="badge bg-secondary">{{ $att->status ?? 'N/A' }}</span>
                        @endif
                    </td>
                    <td>{{ $att->attendance_date ? \Carbon\Carbon::parse($att->attendance_date)->format('d-m-Y') : '-' }}</td>
                    <td>
                        <a href="{{ route('attendance.show', $att->user_id) }}" class="btn btn-sm btn-outline-primary">
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

<div class="d-flex justify-content-center mt-3">
    {!! $attendance->appends(request()->query())->links('pagination::bootstrap-5') !!}
</div>
