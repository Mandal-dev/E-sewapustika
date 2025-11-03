<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@if (isset($error) && $error)
    <div class="alert alert-danger mt-2">
        {{ $error }}
    </div>
@endif

@php
    $designation = Session::get('user.designation_type');
@endphp

<!-- ================= Desktop Table ================= -->
<div class="table-responsive d-none d-md-block" style="max-height:400px;overflow-y:auto;padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        <thead class="table-light">
            <tr>
                <th>{{ __('messages.serial_no') }}</th>
                <th>{{ __('messages.department') }}</th>
                <th>{{ __('messages.police_name') }}</th>
                <th>{{ __('messages.buckle_number') }}</th>
                <th>{{ __('messages.increment_date') }}</th>
                <th>{{ __('messages.present_days') }}</th>
                <th>{{ __('messages.increment_type') }}</th>
                <th>{{ __('messages.level') }}</th>
                <th>{{ __('messages.grade_pay') }}</th>
                <th>{{ __('messages.new_salary') }}</th>
                <th>{{ __('messages.increased_amount') }}</th>
                <th>{{ __('messages.document') }}</th>
                @if ($designation === 'Head_Person')
                    <th>{{ __('messages.action') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($increments as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->station_name ?? '-' }}</td>
                    <td>{{ $item->police_name ?? '--' }}</td>
                    <td>{{ $item->buckle_number ?? '--' }}</td>
                    <td>{{ $item->increment_date ? \Carbon\Carbon::parse($item->increment_date)->format('d-m-Y') : '--' }}</td>
                    <td>{{ $item->present_days ?? '--' }}</td>
                    <td>{{ $item->increment_type ?? '--' }}</td>
                    <td>{{ $item->level ?? '--' }}</td>
                    <td>{{ $item->grade_pay ?? '--' }}</td>
                    <td>{{ $item->new_salary ?? '--' }}</td>
                    <td>{{ $item->increased_amount ?? '--' }}</td>
                    <td class="text-center">
                        @if ($item->increment_documents)
                            <a href="{{ route('salary_increment.view', $item->increment_documents) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('messages.not_available') }}</span>
                        @endif
                    </td>
                    @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm" onclick="openModal('{{ route('salary_increment.add', $item->police_user_id) }}')" title="{{ __('messages.add_increment') }}" style="padding: 6px 10px; border-radius: 50%;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center text-muted">{{ __('messages.no_record_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ================= Mobile Card View ================= -->
<div class="d-md-none">
    @forelse($increments as $index => $item)
        <div class="card mb-3 shadow-sm rounded-3 border-0">
            <!-- Card Header -->
            <div class="card-header d-flex justify-content-between align-items-center" style="background: rgb(233, 245, 255); border-radius: 0.75rem 0.75rem 0 0;">
                <span><strong>#{{ $index + 1 }} - {{ $item->police_name ?? '--' }}</strong></span>
                @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                    <button class="btn btn-primary btn-sm" onclick="openModal('{{ route('salary_increment.add', $item->police_user_id) }}')" title="{{ __('messages.add_increment') }}" style="padding: 4px 8px; border-radius: 50%;">
                        <i class="fas fa-plus"></i>
                    </button>
                @endif
            </div>

            <!-- Card Body -->
            <div class="card-body p-3">
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.department') }}:</strong> {{ $item->station_name ?? '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.buckle_number') }}:</strong> {{ $item->buckle_number ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.increment_date') }}:</strong> {{ $item->increment_date ? \Carbon\Carbon::parse($item->increment_date)->format('d-m-Y') : '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.present_days') }}:</strong> {{ $item->present_days ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.increment_type') }}:</strong> {{ $item->increment_type ?? '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.level') }}:</strong> {{ $item->level ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.grade_pay') }}:</strong> {{ $item->grade_pay ?? '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.new_salary') }}:</strong> {{ $item->new_salary ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.increased_amount') }}:</strong> {{ $item->increased_amount ?? '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.reason') }}:</strong> {{ $item->reason ?? '--' }}</div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>{{ __('messages.document') }}:</strong>
                        @if ($item->increment_documents)
                            <a href="{{ route('salary_increment.view', $item->increment_documents) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('messages.not_available') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">{{ __('messages.no_record_found') }}</p>
    @endforelse
</div>
