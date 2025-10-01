@php
    $designation = Session::get('user.designation_type');
@endphp

@forelse($polices as $index => $police)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $police->police_name }}</td>
        <td>{{ $police->buckle_number }}</td>
        <td>{{ $police->role }}</td>
        <td>{{ $police->punishment_type ?? '--' }}</td>
        <td>{{ $police->reason ?? '--' }}</td>
        <td>
            @if ($police->punishment_documents)
                <a href="{{ route('punishments.view', $police->punishment_documents) }}" target="_blank"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> पहा
                </a>
            @else
                <span class="text-muted">नाही</span>
            @endif
        </td>
        <td>
            @php
                $status = strtolower($police->custom_status);
            @endphp

            @if ($status === 'approved')
                <span class="badge bg-success text-white status-badge" style="cursor:pointer"
                    onclick="openReasonModal('मंजूर', 'टीप:', '{{ $police->remark ?? 'No remark provided' }}', 'approved')">
                    मंजूर
                </span>
            @elseif ($status === 'rejected')
                <span class="badge bg-danger text-white status-badge" style="cursor:pointer"
                    onclick="openReasonModal('नाकारले', 'नाकारण्याचे कारण:', '{{ $police->remark ?? 'No remark provided' }}', 'rejected')">
                    नाकारले
                </span>
            @elseif ($status === 'uploaded')
                <span class="badge bg-info text-white status-badge" style="cursor:pointer"
                    onclick="openReasonModal('अपलोड', 'स्थिती:', 'शिक्षा अपलोड झाली आहे, पण पुनरावलोकन अद्याप झाले नाही', 'uploaded')">
                    अपलोड
                </span>
            @else
                {{-- Pending --}}
                <span class="badge bg-warning text-dark status-badge" style="cursor:pointer"
                    onclick="openReasonModal('प्रलंबित', 'स्थिती:', 'शिक्षा अजून प्रलंबित आहे', 'pending')">
                    प्रलंबित
                </span>
            @endif
        </td>
        <td class="d-flex flex-wrap gap-1">
            @if ($designation === 'Head_Person' || $designation === 'Punishment_Department')
                <button class="btn btn-sm btn-primary d-flex align-items-center"
                    onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')">
                    <i class="fas fa-plus me-1"></i>
                </button>
            @endif

            <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                class="btn btn-sm btn-info d-flex align-items-center">
                <i class="fas fa-eye me-1"></i>
            </a>

            @if ($designation === 'Head_Person' && $police->punishment_id && $status === 'uploaded')
                <button class="btn btn-sm btn-success d-flex align-items-center"
                    onclick="openModal('{{ route('punishments.show', $police->punishment_id) }}')">
                    <i class="fas fa-check me-1"></i>
                </button>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">कोणतीही नोंद सापडली नाही</td>
    </tr>
@endforelse

<!-- Reject / Status Modal -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReasonModalTitle">स्थिती</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="rejectReasonModalBody"></div>
        </div>
    </div>
</div>

<script>
    // Status modal
    function openReasonModal(title, label, variable, status) {
        const modal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
        modal.show();

        document.getElementById('rejectReasonModalTitle').innerText = title;

        let color = 'black';
        if (status === 'approved') color = 'green';
        else if (status === 'rejected') color = 'red';
        else if (status === 'pending') color = 'orange';
        else if (status === 'uploaded') color = 'blue';

        document.getElementById('rejectReasonModalBody').innerHTML =
            `<p>${label} <span style="color:${color}; font-weight:bold;">${variable}</span></p>`;
    }
</script>
