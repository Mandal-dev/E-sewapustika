@php
    $designation = Session::get('user.designation_type');
@endphp

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
            @if ($designation === 'Head_Person')
                <button class="btn btn-sm btn-warning"
                        onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')">
                    <i class="fas fa-edit"></i> वेतनवाढ जोडा
                </button>
            @endif

            <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="15" class="text-center">कोणतीही नोंद सापडली नाही</td>
    </tr>
@endforelse
