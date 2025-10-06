{{-- Desktop Table Rows --}}
@php
    $designation = Session::get('user.designation_type');
@endphp
@forelse($polices as $index => $police)
    <tr>
        <td>
            {{ (is_object($polices) && method_exists($polices, 'firstItem') ? $polices->firstItem() : 0) + $index }}
        </td>


        <td>{{ $police->police_name ?? '--' }}</td>
        <td>{{ $police->buckle_number ?? '--' }}</td>
        <td>{{ $police->role ?? '--' }}</td>
        <td>{{ $police->reward_given_date ? \Carbon\Carbon::parse($police->reward_given_date)->format('d-m-Y') : '--' }}
        </td>
        <td>{{ $police->reward_type ?? '--' }}</td>
        <td>{{ $police->reason ?? '--' }}</td>
        <td>
            @if ($police->rewards_documents)
                <a href="{{ route('rewards.view', $police->rewards_documents) }}" target="_blank"
                    class="btn btn-sm btn-danger">
                    <i class="fas fa-file-pdf"></i> पहा
                </a>
            @else
                <span class="text-muted">नाही</span>
            @endif

        </td>
        <td>
            @if (strtolower($police->reward_status) === 'approved')
                <span class="badge bg-success text-white" style="cursor:pointer"
                    data-variable="{{ $police->gadget_number ?? 'No information' }}" data-label="Gadget Number:"
                    data-title="मंजूर">
                    मंजूर
                </span>
            @elseif (strtolower($police->reward_status) === 'rejected')
                <span class="badge bg-danger text-white" style="cursor:pointer"
                    data-variable="{{ $police->reject_reason ?? 'No reason provided' }}" data-label="नाकारले कारण:"
                    data-title="नाकारले">
                    नाकारले
                </span>
            @else
                <span class="badge bg-warning text-dark" style="cursor:pointer" data-variable="Reward is still pending"
                    data-label="Status:" data-title="प्रलंबित">
                    प्रलंबित
                </span>
            @endif



        </td>

        <td class="text-center">
            <div class="d-flex justify-content-center gap-1">
                <!-- Add Button -->
                <button class="btn btn-primary btn-sm menuBtn"
                    onclick="openModal('{{ route('rewards.add', $police->police_user_id) }}')"
                    title="{{ __('messages.add') }}" style="padding: 6px 10px; border-radius: 50%;">
                    <i class="fas fa-plus"></i>
                </button>

                <!-- View Button -->
                <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-info btn-sm"
                    title="{{ __('messages.view_profile') }}" style="padding: 6px 10px; border-radius: 50%;">
                    <i class="fas fa-eye"></i>
                </a>
                @if ($designation === 'Head_Person' && $police->reward_id && strtolower($police->reward_status) === 'pending')
                    <button class="btn btn-sm btn-warning menuBtn" style="padding: 6px 10px; border-radius: 50%;"
                        onclick="openModal('{{ route('aprove.rewards.show', $police->reward_id) }}')">
                        <i class="fas fa-check me-1"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">कोणतीही नोंद सापडली नाही</td>
    </tr>
@endforelse

<!-- Modal for reject reason -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!-- center modal vertically -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReasonModalTitle">कारण</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="rejectReasonModalBody">
                <!-- Reason will be injected here -->
            </div>
        </div>
    </div>
</div>
<!-- ✅ Global Dynamic Modal -->
<div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

<script>
    window.openModal = function(url) {
        const modalEl = document.getElementById('dynamicModal');

        if (!modalEl) {
            console.error("Modal element not found: #dynamicModal");
            return;
        }

        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        const modalBody = modalEl.querySelector('.modal-body');
        modalBody.innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';

        fetch(url)
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
            })
            .catch(() => {
                modalBody.innerHTML = '<div class="alert alert-danger">Failed to load content.</div>';
            });
    };
</script>

<script>
    function openReasonModal(title, label, variable, status) {
        var modal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
        modal.show();

        document.getElementById('rejectReasonModalTitle').innerText = title;

        // Set color based on status
        let color = 'black'; // default
        if (status === 'approved') color = 'green';
        else if (status === 'rejected') color = 'red';
        else if (status === 'pending') color = 'orange';

        document.getElementById('rejectReasonModalBody').innerHTML =
            `<p>${label} <span style="color:${color}; font-weight:bold;">${variable}</span></p>`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('span[data-variable]').forEach(function(el) {
            el.addEventListener('click', function() {
                let title = this.getAttribute('data-title') || 'कारण';
                let label = this.getAttribute('data-label') || '';
                let variable = this.getAttribute('data-variable') || '';
                let status = this.classList.contains('bg-success') ? 'approved' :
                    this.classList.contains('bg-danger') ? 'rejected' : 'pending';
                openReasonModal(title, label, variable, status);
            });
        });
    });
</script>
