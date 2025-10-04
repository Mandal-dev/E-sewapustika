@php
    $designation = Session::get('user.designation_type');
@endphp
<div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
    @forelse($polices as $index => $police)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $police->police_station_name ?? '--' }}</td>
            <td>{{ $police->police_name ?? '--' }}</td>
            <td>{{ $police->buckle_number ?? '--' }}</td>
            <td>{{ $police->designation_type ?? '--' }}</td>
            <td>{{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}</td>
            <td>{{ $police->increment_type ?? '--' }}</td>
            <td>{{ $police->level ?? '--' }}</td>
            <td>{{ $police->grade_pay ?? '--' }}</td>
            <td>{{ $police->new_salary ?? '--' }}</td>
            <td>{{ $police->increased_amount ?? '--' }}</td>
            <td>
                @if ($police->increment_documents)
                    <a href="{{ route('salary_increment.view', $police->increment_documents) }}" target="_blank"
                        class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                    </a>
                @else
                    <span class="text-muted">{{ __('messages.no_document') }}</span>
                @endif
            </td>
            <td>
                @php
                    $status = strtolower($police->salary_status);
                @endphp

                @if ($status === 'approved')
                    <span class="badge bg-success text-white status-badge" style="cursor:pointer"
                        data-variable="{{ $police->remark ?? __('messages.not_available') }}"
                        data-label="{{ __('messages.remark') }}:" data-title="{{ __('messages.approved') }}">
                        {{ __('messages.approved') }}
                    </span>
                @elseif ($status === 'rejected')
                    <span class="badge bg-danger text-white status-badge" style="cursor:pointer"
                        data-variable="{{ $police->remark ?? __('messages.not_available') }}"
                        data-label="{{ __('messages.reject_reason') }}:" data-title="{{ __('messages.rejected') }}">
                        {{ __('messages.rejected') }}
                    </span>
                @elseif ($status === 'uploaded')
                    <span class="badge bg-info text-white status-badge" style="cursor:pointer"
                        data-variable="{{ __('messages.uploaded') }} {{ __('messages.pending') }}"
                        data-label="{{ __('messages.status') }}:" data-title="{{ __('messages.uploaded') }}">
                        {{ __('messages.uploaded') }}
                    </span>
                @else
                    <span class="badge bg-warning text-dark status-badge" style="cursor:pointer"
                        data-variable="{{ __('messages.pending') }}" data-label="{{ __('messages.status') }}:"
                        data-title="{{ __('messages.pending') }}">
                        {{ __('messages.pending') }}
                    </span>
                @endif
            </td>
            <td class="text-center">
                <div class="d-flex justify-content-center gap-1">
                    @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                        <button class="btn btn-primary btn-sm"
                            onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')"
                            title="{{ __('messages.add_increment') }}" style="padding: 6px 10px; border-radius: 50%;">
                            <i class="fas fa-plus"></i>
                        </button>
                    @endif

                    <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-info btn-sm"
                        title="{{ __('messages.view_profile') }}" style="padding: 6px 10px; border-radius: 50%;">
                        <i class="fas fa-eye"></i>
                    </a>

                    @if ($designation === 'Head_Person' && $police->salary_increment_id && strtolower($police->salary_status) === 'pending')
                        <button class="btn btn-sm btn-warning " style="padding: 6px 10px; border-radius: 50%;"
                            onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')"
                            title="{{ __('messages.approve') }}">
                            <i class="fas fa-check me-1"></i>
                        </button>
                    @endif
                </div>
            </td>
        </tr>
</div>

<!-- Mobile Card View -->
<div class="officer-card d-md-none p-3 mb-3 border rounded shadow-sm">
    <div class="left-col mb-2">
        <p><strong>{{ __('messages.department') }}:</strong> {{ $police->police_station_name ?? '--' }}</p>
        <p><strong>{{ __('messages.police_name') }}:</strong> {{ $police->police_name ?? '--' }}</p>
        <p><strong>{{ __('messages.buckle_number') }}:</strong> {{ $police->buckle_number ?? '--' }}</p>
        <p><strong>{{ __('messages.designation') }}:</strong> {{ $police->designation_type ?? '--' }}</p>
        <p><strong>{{ __('messages.increment_date') }}:</strong>
            {{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}
        </p>
        <p><strong>{{ __('messages.increment_type') }}:</strong> {{ $police->increment_type ?? '--' }}</p>
    </div>

    <div class="right-col text-start mb-2">
        <p><strong>{{ __('messages.level_no') }}:</strong> {{ $police->level ?? '--' }}</p>
        <p><strong>{{ __('messages.grade_pay') }}:</strong> {{ $police->grade_pay ?? '--' }}</p>
        <p><strong>{{ __('messages.net_salary') }}:</strong> {{ $police->new_salary ?? '--' }}</p>
        <p><strong>{{ __('messages.increased_amount') }}:</strong> {{ $police->increased_amount ?? '--' }}</p>

        @if ($police->increment_documents)
            <a href="{{ route('salary_increment.view', $police->increment_documents) }}" target="_blank"
                class="btn btn-sm btn-danger mb-2">
                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
            </a>
        @else
            <p><span class="text-muted">{{ __('messages.no_document') }}</span></p>
        @endif
    </div>

    <div class="action-buttons">
        @if ($designation === 'Head_Person' || $designation === 'Account_Department')
            <button class="btn btn-sm btn-warning mb-2"
                onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')">
                <i class="fas fa-plus"></i> {{ __('messages.add_increment') }}
            </button>
        @endif
        @if ($designation === 'Head_Person' && $police->salary_increment_id && strtolower($police->salary_status) === 'pending')
            <button class="btn btn-sm btn-warning d-flex align-items-center"
                onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')">
                <i class="fas fa-check me-1"></i>
                {{ __('messages.approve') }}
            </button>
        @endif
        <a class="btn btn-sm btn-info mb-2" href="{{ route('police_profile.index', $police->police_user_id) }}">
            <i class="fas fa-eye"></i>
        </a>
    </div>
</div>

@empty
<tr>
    <td colspan="15" class="text-center">{{ __('messages.no_records_found') }}</td>
</tr>
@endforelse
