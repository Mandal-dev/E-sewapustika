@php
    $designation = Session::get('user.designation_type');
@endphp

<div class="dashboard-content">
    <!-- Table for Desktop -->
    <div class="table-responsive d-none d-md-block" style="max-height:400px; overflow-y:auto; padding:10px;">
        <table class="table table-bordered align-middle my-rounded-table">
            <thead class="table-light">
                <tr>
                    <th>{{ __('messages.sr_no') }}</th>
                    <th>{{ __('messages.department') }}</th>
                    <th>{{ __('messages.post') }}</th>
                    <th>{{ __('messages.police_name') }}</th>
                    <th>{{ __('messages.mobile_no') }}</th>
                    <th>{{ __('messages.buckle_no') }}</th>
                    <th>{{ __('messages.sewa_pustika') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($polices as $index => $police)
                    <tr>
                        <td>{{ $polices->firstItem() + $index }}</td>
                        <td>{{ $police->police_station_name }}</td>
                        <td>{{ $police->post }}</td>
                        <td>{{ $police->police_name }}</td>
                        <td>{{ $police->mobile }}</td>
                        <td>{{ $police->buckle_number }}</td>
                        <td>
                            @if ($police->sewapusticapath)
                                <a href="{{ route('sewapustika.view', $police->sewapusticapath) }}"
                                    target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                                </a>
                            @else
                                <span class="text-muted">{{ __('messages.none') }}</span>
                            @endif
                        </td>
                        <td>
                            @php $status = strtolower($police->review_status); @endphp
                            @if ($status === 'approved')
                                <span class="badge bg-success text-white status-badge" style="cursor:pointer"
                                    data-variable="{{ $police->remark ?? __('messages.no_remark') }}"
                                    data-label="{{ __('messages.note') }}" data-title="{{ __('messages.approved') }}">
                                    {{ __('messages.approved') }}
                                </span>
                            @elseif ($status === 'rejected')
                                <span class="badge bg-danger text-white status-badge" style="cursor:pointer"
                                    data-variable="{{ $police->remark ?? __('messages.no_remark') }}"
                                    data-label="{{ __('messages.reason') }}" data-title="{{ __('messages.rejected') }}">
                                    {{ __('messages.rejected') }}
                                </span>
                            @elseif ($status === 'uploaded')
                                <span class="badge bg-info text-white status-badge" style="cursor:pointer"
                                    data-variable="{{ __('messages.uploaded_not_reviewed') }}"
                                    data-label="{{ __('messages.status') }}" data-title="{{ __('messages.uploaded') }}">
                                    {{ __('messages.uploaded') }}
                                </span>
                            @else
                                <span class="badge bg-warning text-dark status-badge" style="cursor:pointer"
                                    data-variable="{{ __('messages.pending_status') }}"
                                    data-label="{{ __('messages.status') }}" data-title="{{ __('messages.pending') }}">
                                    {{ __('messages.pending') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Add Button -->
                                <button class="btn btn-primary btn-sm menuBtn"
                                    data-url="{{ route('sewa_pustika.addshow', $police->police_user_id) }}"
                                    title="{{ __('messages.add') }}" style="padding: 6px 10px; border-radius: 50%;">
                                    <i class="fas fa-plus"></i>
                                </button>

                                <!-- View Button -->
                                <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                    class="btn btn-info btn-sm" title="{{ __('messages.view_profile') }}"
                                    style="padding: 6px 10px; border-radius: 50%;">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if ($designation === 'Head_Person' && $police->sewapustika_id && $status === 'pending')
                                    <button class="btn btn-sm btn-warning menuBtn"
                                        style="padding: 6px 10px; border-radius: 50%;"
                                        data-url="{{ route('sewapustika.approval.show', $police->sewapustika_id) }}">
                                        <i class="fas fa-check me-1"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">{{ __('messages.no_record_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="d-md-none">
        @forelse($polices as $police)
            <div class="officer-card mb-3 p-3 border rounded">
                <div class="left-col mb-2">
                    <p><strong>{{ __('messages.department') }}:</strong> {{ $police->police_station_name }}</p>
                    <p><strong>{{ __('messages.post') }}:</strong> {{ $police->post }}</p>
                    <p><strong>{{ __('messages.police_name') }}:</strong> {{ $police->police_name }}</p>
                    <p><strong>{{ __('messages.mobile_no') }}:</strong> {{ $police->mobile }}</p>
                    <p><strong>{{ __('messages.buckle_no') }}:</strong> {{ $police->buckle_number }}</p>
                </div>
                <div class="right-col d-flex flex-wrap gap-2">
                    @if ($police->sewapusticapath)
                        <a href="{{ route('sewapustika.view', $police->sewapusticapath) }}"
                            class="btn btn-sm btn-primary flex-grow-1">
                            <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                        </a>
                    @else
                        <span class="text-muted">{{ __('messages.none') }}</span>
                    @endif

                    <!-- Add -->
                    <button class="btn btn-sm btn-primary menuBtn flex-grow-1"
                        data-url="{{ route('sewa_pustika.addshow', $police->police_user_id) }}">
                        <i class="fas fa-plus"></i> {{ __('messages.add') }}
                    </button>

                    <!-- View Profile -->
                    <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                        class="btn btn-sm btn-info flex-grow-1">
                        <i class="fas fa-eye"></i> {{ __('messages.view_profile') }}
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center text-muted">{{ __('messages.no_record_found') }}</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            {{ __('messages.showing') }} {{ $polices->firstItem() }} {{ __('messages.to') }} {{ $polices->lastItem() }}
            {{ __('messages.of') }} {{ $polices->total() }} {{ __('messages.records') }}
            ({{ __('messages.page') }} {{ $polices->currentPage() }} {{ __('messages.of') }} {{ $polices->lastPage() }})
        </div>
        <div>
            {!! $polices->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
