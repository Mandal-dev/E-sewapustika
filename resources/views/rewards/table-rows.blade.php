{{-- Desktop Table Rows --}}
@php
    $designation = Session::get('user.designation_type');
@endphp
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
            @if ($designation === 'Head_Person')
                @if ($police->reward_id)
                    @if (strtolower($police->reward_status) === 'pending')
                        <button class="btn btn-sm btn-success"
                            onclick="openModal('{{ route('aprove.rewards.show', $police->reward_id) }}')">
                            <i class="fas fa-check-circle"></i> मंजूर करा
                        </button>
                    @endif
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
<script>
    function openModal(url) {
        // Show modal first
        var modal = new bootstrap.Modal(document.getElementById('dynamicModal'));
        modal.show();

        // Load content via AJAX
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.querySelector("#dynamicModal .modal-body").innerHTML = html;
            })
            .catch(() => {
                document.querySelector("#dynamicModal .modal-body").innerHTML =
                    '<div class="alert alert-danger">Unable to load content.</div>';
            });
    }
</script>
