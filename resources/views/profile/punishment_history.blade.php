<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Desktop Table -->
<div class="table-responsive d-none d-md-block" style="max-height:400px; overflow-y:auto; padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        @php
            $designation = Session::get('user.designation_type');
        @endphp
        <thead class="table-light">
            <tr>
                <th>{{ __('messages.serial_no') }}</th>
                <th>{{ __('messages.police_name') }}</th>
                <th>{{ __('messages.buckle_number') }}</th>
                <th>{{ __('messages.punishment_date') }}</th>
                <th>{{ __('messages.punishment_type') }}</th>
                <th>{{ __('messages.reason') }}</th>
                <th>{{ __('messages.document') }}</th>
                @if ($designation === 'Head_Person')
                    <th>{{ __('messages.action') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($punishments as $index => $police)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $police->police_name ?? '--' }}</td>
                    <td>{{ $police->buckle_number ?? '--' }}</td>
                    <td>{{ $police->punishment_given_date ? \Carbon\Carbon::parse($police->punishment_given_date)->format('d-m-Y') : '--' }}</td>
                    <td>{{ $police->punishment_type ?? '--' }}</td>
                    <td>{{ $police->reason ?? '--' }}</td>
                    <td>
                        @if ($police->punishment_documents)
                            <a href="{{ route('punishments.view', $police->punishment_documents) }}" target="_blank"
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('messages.not_available') }}</span>
                        @endif
                    </td>
                    @if ($designation === 'Head_Person' || $designation === 'Punishment_Department')
                        <td>
                            <button class="btn btn-primary btn-sm"
                                onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')">
                                <i class="fas fa-plus"></i> {{ __('messages.add_punishment') }}
                            </button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">{{ __('messages.no_record_found') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Mobile Card View -->
<div class="d-md-none">
    @forelse($punishments as $index => $police)
        <div class="card mb-3 shadow-sm rounded-3 border-0">
            <div class="card-header" style="background: rgb(233, 245, 255);">
                <strong>#{{ $index + 1 }} - {{ $police->police_name ?? '--' }}</strong>
            </div>
            <div class="card-body p-3">
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.buckle_number') }}:</strong> {{ $police->buckle_number ?? '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.punishment_date') }}:</strong>
                        {{ $police->punishment_given_date ? \Carbon\Carbon::parse($police->punishment_given_date)->format('d-m-Y') : '--' }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>{{ __('messages.punishment_type') }}:</strong> {{ $police->punishment_type ?? '--' }}</div>
                    <div class="col-6"><strong>{{ __('messages.reason') }}:</strong> {{ $police->reason ?? '--' }}</div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>{{ __('messages.document') }}:</strong>
                        @if ($police->punishment_documents)
                            <a href="{{ route('punishments.view', $police->punishment_documents) }}" target="_blank"
                                class="btn btn-sm btn-danger py-0 px-2">
                                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                            </a>
                        @else
                            <span class="text-muted">{{ __('messages.not_available') }}</span>
                        @endif
                    </div>
                </div>
                @if ($designation === 'Head_Person' || $designation === 'Punishment_Department')
                    <div class="row mt-2">
                        <div class="col-12">
                            <button class="btn btn-primary btn-sm"
                                onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')">
                                <i class="fas fa-plus"></i> {{ __('messages.add_punishment') }}
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-center text-muted">{{ __('messages.no_record_found') }}</p>
    @endforelse
</div>
