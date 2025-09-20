{{-- Desktop Table Rows --}}
@forelse($polices as $index => $police)
    <tr>
        <td>{{ $polices->firstItem() + $index }}</td>
        <td>{{ $police->police_name ?? '--' }}</td>
        <td>{{ $police->buckle_number ?? '--' }}</td>
        <td>{{ $police->role ?? '--' }}</td>
        <td>{{ $police->reward_given_date ? \Carbon\Carbon::parse($police->reward_given_date)->format('d-m-Y') : '--' }}
        </td>
        <td>{{ $police->reward_type ?? '--' }}</td>
        <td>{{ $police->reason ?? '--' }}</td>
        <td>
            @if ($police->rewards_documents)
                <a href="{{ asset('uploads/rewards/' . $police->rewards_documents) }}" target="_blank"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> पहा
                </a>
            @else
                <span class="text-muted">नाही</span>
            @endif
        </td>
        <td>
            @if (strtolower($police->reward_status) === 'approved')
                <span class="badge bg-success">मंजूर</span>
            @elseif (strtolower($police->reward_status) === 'rejected')
                <span class="badge bg-danger">नाकारले</span>
            @else
                <span class="badge bg-warning text-dark">प्रलंबित</span>
            @endif
        </td>
        <td>
            <button class="btn btn-sm btn-warning"
                onclick="openModal('{{ route('rewards.add', $police->police_user_id) }}')">
                <i class="fas fa-edit"></i> बक्षीस जोडा
            </button>

            @if ($police->reward_id)
                @if (strtolower($police->reward_status) === 'pending')
                    <button class="btn btn-sm btn-success"
                        onclick="aproveopenModal('{{ route('aprove.rewards.show', $police->reward_id) }}')">
                        <i class="fas fa-check-circle"></i> मंजूर करा
                    </button>
                @elseif(strtolower($police->reward_status) === 'rejected')
                    <button class="btn btn-sm btn-danger"
                        onclick="viewRejectReason({{ json_encode($police->reason ?? 'No reason provided') }})">
                        <i class="fas fa-eye"></i> कारण पहा
                    </button>
                @endif
            @endif

            <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">कोणतीही नोंद सापडली नाही</td>
    </tr>
@endforelse

{{-- Mobile Cards --}}
@forelse($polices as $police)
    <div class="officer-card d-md-none p-3 mb-3 border rounded shadow-sm">
        <p><strong>Police Name:</strong> {{ $police->police_name ?? '--' }}</p>
        <p><strong>Buckle No:</strong> {{ $police->buckle_number ?? '--' }}</p>
        <p><strong>Role:</strong> {{ $police->role ?? '--' }}</p>
        <p><strong>Reward Date:</strong>
            {{ $police->reward_given_date ? \Carbon\Carbon::parse($police->reward_given_date)->format('d-m-Y') : '--' }}
        </p>
        <p><strong>Reward Type:</strong> {{ $police->reward_type ?? '--' }}</p>

        <div class="mb-2">
            <button class="btn btn-sm btn-warning mb-1"
                onclick="openModal('{{ route('rewards.add', $police->police_user_id) }}')">
                <i class="fas fa-plus"></i> बक्षीस जोडा
            </button>

            <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-sm btn-info mb-1">
                <i class="fas fa-eye"></i> view
            </a>

            @if ($police->reward_id)
                @if (strtolower($police->reward_status) === 'pending')
                    <button class="btn btn-sm btn-success mb-1"
                        onclick="aproveopenModal('{{ route('aprove.rewards.show', $police->reward_id) }}')">
                        <i class="fas fa-check-circle"></i> मंजूर करा
                    </button>
                @elseif(strtolower($police->reward_status) === 'rejected')
                    <button class="btn btn-sm btn-danger mb-1"
                        onclick="viewRejectReason({{ json_encode($police->reason ?? 'No reason provided') }})">
                        <i class="fas fa-eye"></i> कारण पहा
                    </button>
                @endif
            @endif

            @if ($police->rewards_documents)
                <a href="{{ asset('uploads/rewards/' . $police->rewards_documents) }}" target="_blank"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> पहा
                </a>
            @else
                <span class="text-muted">नाही</span>
            @endif

            <p>
                @if (strtolower($police->reward_status) === 'approved')
                    <span class="badge bg-success">मंजूर</span>
                @elseif (strtolower($police->reward_status) === 'rejected')
                    <span class="badge bg-danger">नाकारले</span>
                @else
                    <span class="badge bg-warning text-dark">प्रलंबित</span>
                @endif
            </p>
        </div>
    </div>
@empty
    <div class="text-center p-3 d-md-none">कोणतीही नोंद सापडली नाही</div>
@endforelse
