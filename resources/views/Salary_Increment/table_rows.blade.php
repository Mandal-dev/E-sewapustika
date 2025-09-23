@php
    $designation = Session::get('user.designation_type');
@endphp
<div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
@forelse($polices as $index => $police)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $police->city_name ?? '--' }}</td>
        <td>{{ $police->police_name ?? '--' }}</td>
        <td>{{ $police->buckle_number ?? '--' }}</td>
        <td>{{ $police->designation_type ?? '--' }}</td>
        <td>{{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}</td>
        <td>{{ $police->increment_type ?? '--' }}</td>
        <td>{{ $police->level ?? '--' }}</td>
        <td>{{ $police->grade_pay ?? '--' }}</td>
        <td>{{ $police->new_salary ?? '--' }}</td>s;
        <td>{{ $police->increased_amount ?? '--' }}</td>
        <td>
            @if ($police->increment_documents)
                <a href="{{ route('salary_increment.view', $police->increment_documents) }}" target="_blank"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> पहा
                </a>
            @else
                <span class="text-muted">नाही</span>
            @endif
        </td>
        <td>
          @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                <button class="btn btn-sm btn-warning"
                    onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')">
                    <i class="fas fa-plus"></i> वेतनवाढ जोडा
                </button>
            @endif
               @if ($designation === 'Head_Person' && $police->salary_increment_id && strtolower($police->salary_status) === 'pending')
                 <button class="btn btn-sm btn-warning d-flex align-items-center"
                        onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')">
                        <i class="fas fa-check me-1"></i>
                        मंजूर करा
                    </button>
            @endif

            <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>
</div>
    <!-- Mobile Card View -->
    <div class="officer-card d-md-none p-3 mb-3 border rounded shadow-sm">
        <div class="left-col mb-2">
            <p><strong>City:</strong> {{ $police->city_name ?? '--' }}</p>
            <p><strong>Police Name:</strong> {{ $police->police_name ?? '--' }}</p>
            <p><strong>Buckle No:</strong> {{ $police->buckle_number ?? '--' }}</p>
            <p><strong>Designation:</strong> {{ $police->designation_type ?? '--' }}</p>
            <p><strong>Increment Date:</strong>
                {{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}
            </p>
            <p><strong>Increment Type:</strong> {{ $police->increment_type ?? '--' }}</p>
        </div>

        <div class="right-col text-start mb-2">
            <p><strong>Level:</strong> {{ $police->level ?? '--' }}</p>
            <p><strong>Grade Pay:</strong> {{ $police->grade_pay ?? '--' }}</p>
            <p><strong>New Salary:</strong> {{ $police->new_salary ?? '--' }}</p>
            <p><strong>Increased Amount:</strong> {{ $police->increased_amount ?? '--' }}</p>

            @if ($police->increment_documents)
                <a href="{{ route('salary_increment.view', $police->increment_documents) }}" target="_blank"
                    class="btn btn-sm btn-danger mb-2">
                    <i class="fas fa-file-pdf"></i> पहा
                </a>
            @else
                <p><span class="text-muted">नाही</span></p>
            @endif
        </div>

        <div class="action-buttons">
           @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                <button class="btn btn-sm btn-warning mb-2"
                    onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')">
                    <i class="fas fa-plus"></i> Add Increment
                </button>
            @endif
           @if ($designation === 'Head_Person' && $police->salary_increment_id && strtolower($police->salary_status) === 'pending')
                 <button class="btn btn-sm btn-warning d-flex align-items-center"
                        onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')">
                        <i class="fas fa-check me-1"></i>
                        मंजूर करा
                    </button>
            @endif
            <a class="btn btn-sm btn-info mb-2" href="{{ route('police_profile.index', $police->police_user_id) }}">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>

    @empty
        <tr>
            <td colspan="15" class="text-center">कोणतीही नोंद सापडली नाही</td>
        </tr>
@endforelse
