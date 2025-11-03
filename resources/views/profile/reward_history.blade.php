<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $designation = Session::get('user.designation_type');
@endphp

<!-- ================= Desktop Table ================= -->
<div class="table-responsive d-none d-md-block" style="max-height:400px; overflow-y:auto; padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        <thead class="table-light">
            <tr>
                <th>{{ __('messages.serial_no') }}</th>
                <th>{{ __('messages.police_name') }}</th>
                <th>{{ __('messages.buckle_number') }}</th>
                <th>{{ __('messages.role') }}</th>
                <th>{{ __('messages.reward_date') }}</th>
                <th>{{ __('messages.reward_type') }}</th>
                <th>{{ __('messages.reason') }}</th>
                <th>{{ __('messages.document') }}</th>
                <th>{{ __('messages.status') }}</th>
                <th>{{ __('messages.action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rewards as $index => $reward)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $reward->police_name ?? '--' }}</td>
                    <td>{{ $reward->buckle_number ?? '--' }}</td>
                    <td>{{ $reward->role ?? '--' }}</td>
                    <td>{{ $reward->reward_given_date ? \Carbon\Carbon::parse($reward->reward_given_date)->format('d-m-Y') : '--' }}</td>
                    <td>{{ $reward->reward_type ?? '--' }}</td>
                    <td>{{ $reward->reason ?? '--' }}</td>
                    <td>
                        @if ($reward->rewards_documents)
                            <a href="{{ route('rewards.view', $reward->rewards_documents) }}" target="_blank"
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('messages.not_available') }}</span>
                        @endif
                    </td>
                    <td>
                        @if (strtolower($reward->reward_status) === 'approved')
                            <span class="badge bg-success">{{ __('messages.approved') }}</span>
                        @elseif(strtolower($reward->reward_status) === 'rejected')
                            <span class="badge bg-danger">{{ __('messages.rejected') }}</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ __('messages.pending') }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($designation === 'Head_Person' || $designation === 'Rewards_Department')
                            <button class="btn btn-primary btn-sm"
                                onclick="openModal('{{ route('rewards.add', $reward->police_user_id ?? 0) }}')">
                                <i class="fas fa-plus"></i> {{ __('messages.add_reward') }}
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">{{ __('messages.no_record_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ================= Mobile Card View ================= -->
<div class="d-md-none">
    @forelse($rewards as $index => $reward)
        <div class="card mb-3 shadow-sm rounded-3 border-0">
            <!-- Card Header -->
            <div class="card-header d-flex justify-content-between align-items-center"
                style="background-color: #e9f5ff; border-radius: 0.75rem 0.75rem 0 0; font-weight: 600;">
                <span>#{{ $index + 1 }} - {{ $reward->police_name ?? '--' }}</span>
                @if ($designation === 'Head_Person' || $designation === 'Rewards_Department')
                    <button class="btn btn-primary btn-sm"
                        onclick="openModal('{{ route('rewards.add', $reward->police_user_id) }}')"
                        title="{{ __('messages.add_reward') }}"
                        style="padding: 4px 8px; border-radius: 50%;">
                        <i class="fas fa-plus"></i>
                    </button>
                @endif
            </div>

            <!-- Card Body -->
            <div class="card-body p-3">
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.buckle_number') }}:</strong> {{ $reward->buckle_number ?? '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.role') }}:</strong> {{ $reward->role ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.reward_date') }}:</strong>
                        {{ $reward->reward_given_date ? \Carbon\Carbon::parse($reward->reward_given_date)->format('d-m-Y') : '--' }}
                    </div>
                    <div class="col-6"><strong>{{ __('messages.reward_type') }}:</strong> {{ $reward->reward_type ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-12"><strong>{{ __('messages.reason') }}:</strong> {{ $reward->reason ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>{{ __('messages.document') }}:</strong>
                        @if ($reward->rewards_documents)
                            <a href="{{ route('rewards.view', $reward->rewards_documents) }}" target="_blank"
                                class="btn btn-sm btn-danger py-0 px-2">
                                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('messages.not_available') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>{{ __('messages.status') }}:</strong>
                        @if (strtolower($reward->reward_status) === 'approved')
                            <span class="badge bg-success">{{ __('messages.approved') }}</span>
                        @elseif(strtolower($reward->reward_status) === 'rejected')
                            <span class="badge bg-danger">{{ __('messages.rejected') }}</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ __('messages.pending') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">{{ __('messages.no_record_found') }}</p>
    @endforelse
</div>
