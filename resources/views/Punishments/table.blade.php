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
                    <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                </a>
            @else
                <span class="text-muted">{{ __('messages.na') }}</span>
            @endif
        </td>
        <td>
            @php
                $status = strtolower($police->custom_status);
            @endphp

            @if ($status === 'approved')
                <span class="badge bg-success text-white status-badge" style="cursor:pointer"
                    onclick="openReasonModal('{{ __('messages.approved') }}', '{{ __('messages.remark') }}:', '{{ $police->remark ?? __('messages.no_remark') }}', 'approved')">
                    {{ __('messages.approved') }}
                </span>
            @elseif ($status === 'rejected')
                <span class="badge bg-danger text-white status-badge" style="cursor:pointer"
                    onclick="openReasonModal('{{ __('messages.rejected') }}', '{{ __('messages.reject_reason') }}:', '{{ $police->remark ?? __('messages.no_remark') }}', 'rejected')">
                    {{ __('messages.rejected') }}
                </span>
            @elseif ($status === 'uploaded')
                <span class="badge bg-info text-white status-badge" style="cursor:pointer"
                    onclick="openReasonModal('{{ __('messages.uploaded') }}', '{{ __('messages.status') }}:', '{{ __('messages.uploaded_pending_review') }}', 'uploaded')">
                    {{ __('messages.uploaded') }}
                </span>
            @else
                <span class="badge bg-warning text-dark status-badge" style="cursor:pointer"
                    onclick="openReasonModal('{{ __('messages.pending') }}', '{{ __('messages.status') }}:', '{{ __('messages.pending_message') }}', 'pending')">
                    {{ __('messages.pending') }}
                </span>
            @endif
        </td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
                <!-- Add Button -->
                <button class="btn btn-primary btn-sm menuBtn"
                    onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')"
                    title="{{ __('messages.add') }}" style="padding: 6px 10px; border-radius: 50%;">
                    <i class="fas fa-plus"></i>
                </button>

                <!-- View Button -->
                <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-info btn-sm"
                    title="{{ __('messages.view_profile') }}" style="padding: 6px 10px; border-radius: 50%;">
                    <i class="fas fa-eye"></i>
                </a>

                @if ($designation === 'Head_Person' && $police->punishment_id && strtolower($police->punishment_status) === 'pending')
                    <button class="btn btn-sm btn-warning menuBtn" style="padding: 6px 10px; border-radius: 50%;"
                        onclick="openModal('{{ route('punishments.show', $police->punishment_id) }}')">
                        <i class="fas fa-check me-1"></i>
                    </button>
                @endif
            </div>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">{{ __('messages.no_records_found') }}</td>
    </tr>
@endforelse

<!-- Reject / Status Modal -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReasonModalTitle">{{ __('messages.status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="rejectReasonModalBody"></div>
        </div>
    </div>
</div>

<script>
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
