@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="app-content p-3">
        @php
            $designation = Session::get('user.designation_type');
        @endphp

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ __('messages.flash_success') }}</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ __('messages.flash_error') }}</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Salary Increment Cards -->
        @if ($designation === 'Head_Person' || $designation === 'Punishment_Department')
            <div class="show-cards mb-3">
                <!-- Cards loaded via AJAX -->
            </div>
        @endif

        <div class="table-section p-3 bg-white rounded shadow-sm">
            <!-- Table Header & Search -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <h5 class="mb-2 fw-semibold">{{ __('messages.shiksha') }} {{ __('messages.list') ?? '' }}</h5>
                </div>

                <div class="search-container">
                    <input type="text" id="searchKeyword" placeholder="{{ __('messages.search_placeholder') }}">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>

            <!-- Desktop Table -->
            <div id="policeTable">
                <div class="table-responsive d-none d-md-block" style="max-height:400px;overflow-y:auto;padding:10px;">
                    <table class="table table-bordered align-middle my-rounded-table">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('messages.sr_no') }}</th>
                                <th>{{ __('messages.officer_name') }}</th>
                                <th>{{ __('messages.buckle_no') }}</th>
                                <th>{{ __('messages.punishment_date') }}</th>
                                <th>{{ __('messages.type') }}</th>
                                <th>{{ __('messages.reason') }}</th>
                                <th>{{ __('messages.documents') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($polices as $index => $police)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $police->police_name }}</td>
                                    <td>{{ $police->buckle_number }}</td>
                                    <td>{{ $police->punishment_given_date ? \Carbon\Carbon::parse($police->punishment_given_date)->format('d-m-Y') : '--' }}
                                    </td>
                                    <td>{{ $police->punishment_type ?? '--' }}</td>
                                    <td>{{ $police->reason ?? '--' }}</td>
                                    <td>
                                        @if ($police->punishment_documents)
                                            <a href="{{ route('punishments.view', $police->punishment_documents) }}"
                                                target="_blank" class="btn btn-sm btn-danger">
                                                <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('messages.no_document') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower($police->custom_status);
                                        @endphp

                                        @if ($status === 'approved')
                                            <span class="badge bg-success text-white status-badge" style="cursor:pointer"
                                                data-variable="{{ $police->remark ?? __('messages.not_available') }}"
                                                data-label="{{ __('messages.remark') }}"
                                                data-title="{{ __('messages.approve') }}">
                                                {{ __('messages.approved') }}
                                            </span>
                                        @elseif ($status === 'rejected')
                                            <span class="badge bg-danger text-white status-badge" style="cursor:pointer"
                                                data-variable="{{ $police->remark ?? __('messages.not_available') }}"
                                                data-label="{{ __('messages.reject_reason') }}"
                                                data-title="{{ __('messages.reject') }}">
                                                {{ __('messages.rejected') }}
                                            </span>
                                        @elseif ($status === 'uploaded')
                                            <span class="badge bg-info text-white status-badge" style="cursor:pointer"
                                                data-variable="{{ __('messages.uploaded') }}"
                                                data-label="{{ __('messages.status') }}"
                                                data-title="{{ __('messages.uploaded') }}">
                                                {{ __('messages.uploaded') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark status-badge" style="cursor:pointer"
                                                data-variable="{{ __('messages.pending') }}"
                                                data-label="{{ __('messages.status') }}"
                                                data-title="{{ __('messages.pending') }}">
                                                {{ __('messages.pending') }}
                                            </span>
                                        @endif
                                    </td>


                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <!-- Add Button -->
                                            <button class="btn btn-primary btn-sm menuBtn"
                                                onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')"
                                                title="{{ __('messages.add') }}"
                                                style="padding: 6px 10px; border-radius: 50%;">
                                                <i class="fas fa-plus"></i>
                                            </button>

                                            <!-- View Button -->
                                            <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                                class="btn btn-info btn-sm" title="{{ __('messages.view_profile') }}"
                                                style="padding: 6px 10px; border-radius: 50%;">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if ($designation === 'Head_Person' && $police->punishment_id && strtolower($police->punishment_status) === 'pending')
                                                <button class="btn btn-sm btn-warning menuBtn"
                                                    style="padding: 6px 10px; border-radius: 50%;"
                                                    onclick="openModal('{{ route('punishments.show', $police->punishment_id) }}')">
                                                    <i class="fas fa-check me-1"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">{{ __('messages.no_records_found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($polices as $police)
                    <div class="officer-card p-3 mb-3 border rounded shadow-sm">
                        <p><strong>{{ __('messages.police_name') }}:</strong> {{ $police->police_name }}</p>
                        <p><strong>{{ __('messages.buckle_no') }}:</strong> {{ $police->buckle_number }}</p>
                        <p><strong>{{ __('messages.punishment_date') }}:</strong>
                            {{ $police->punishment_given_date ? \Carbon\Carbon::parse($police->punishment_given_date)->format('d-m-Y') : '--' }}
                        </p>
                        <p><strong>{{ __('messages.type') }}:</strong> {{ $police->punishment_type ?? '--' }}</p>
                        <p><strong>{{ __('messages.reason') }}:</strong> {{ $police->reason ?? '--' }}</p>
                        <p>
                            @if ($police->punishment_documents)
                                <a href="{{ route('punishments.view', $police->punishment_documents) }}" target="_blank"
                                    class="btn btn-sm btn-danger">
                                    <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                                </a>
                            @else
                                <span class="text-muted">{{ __('messages.no_document') }}</span>
                            @endif
                        </p>
                        <div class="d-flex gap-2">
                            @if ($designation === 'Head_Person' || $designation === 'Punishment_Department')
                                <button class="btn btn-sm btn-warning"
                                    onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')">
                                    <i class="fas fa-plus"></i> {{ __('messages.add_punishment') }}
                                </button>
                            @endif
                            <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> {{ __('messages.view_profile') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">{{ __('messages.no_records_found') }}</p>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    {{ __('messages.showing_records', [
                        'start' => $polices->firstItem(),
                        'end' => $polices->lastItem(),
                        'total' => $polices->total(),
                        'current' => $polices->currentPage(),
                        'last' => $polices->lastPage(),
                    ]) }}
                </div>
                <div>
                    {!! $polices->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>

        <!-- AJAX Modal -->
        <div class="modal fade" id="sewaPustikaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div id="sewaPustikaModalBody" class="p-4 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('messages.loading') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Reason Modal -->
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
    </div>


    <script>
        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Open AJAX modal
        function openModal(url) {
            const modalElement = document.getElementById('sewaPustikaModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            $('#sewaPustikaModalBody').html(`
            <div class="p-5 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">लोड होत आहे...</span>
                </div>
            </div>
        `);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#sewaPustikaModalBody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error, xhr.responseText);
                    $('#sewaPustikaModalBody').html(`
                    <div class="p-5 text-danger text-center">
                        डेटा लोड करण्यात अडचण आली.
                    </div>
                `);
                }
            });
        }

        // Status modal
        function openReasonModal(title, label, variable, status) {
            const modal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
            modal.show();

            document.getElementById('rejectReasonModalTitle').innerText = title;

            let color = 'black';
            if (status === 'approved') color = 'green';
            else if (status === 'rejected') color = 'red';
            else if (status === 'pending') color = 'orange';

            document.getElementById('rejectReasonModalBody').innerHTML =
                `<p>${label} <span style="color:${color}; font-weight:bold;">${variable}</span></p>`;
        }

        // Attach click events to status badges
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-badge').forEach(function(el) {
                el.addEventListener('click', function() {
                    let title = this.getAttribute('data-title') || 'स्थिती';
                    let label = this.getAttribute('data-label') || '';
                    let variable = this.getAttribute('data-variable') || '';
                    let status = this.classList.contains('bg-success') ? 'approved' :
                        this.classList.contains('bg-danger') ? 'rejected' : 'pending';
                    openReasonModal(title, label, variable, status);
                });
            });

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 4000);

            // Load salary increment cards
            $.ajax({
                url: "{{ route('punishments.cards') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('.show-cards').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error loading salary cards:", error);
                }
            });
        });

        // AJAX search function
        function performSearch() {
            let keyword = $('#searchKeyword').val();
            console.log("Searching keyword:", keyword);

            $.ajax({
                url: "{{ route('punishments.search') }}",
                type: "GET",
                data: {
                    keyword: keyword
                },
                success: function(response) {
                    console.log("AJAX Success:", response);

                    // Inject Blade partial HTML directly into tbody
                    $("#policeTable tbody").html(response);
                },

                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                    $('#policeTable tbody').html(
                        `<tr><td colspan="7" class="text-danger text-center">Server error: ${xhr.status}</td></tr>`
                    );
                }
            });
        }

        // Bind keyup with debounce
        $('#searchKeyword').on('keyup', debounce(performSearch, 500));


        // Function to perform search by keyword/status
        function searchByKeyword(keyword = '') {
            $.ajax({
                url: "{{ route('punishments.search') }}",
                type: "GET",
                data: {
                    keyword: keyword
                },
                success: function(response) {
                    // Inject Blade partial HTML directly into tbody
                    $("#policeTable tbody").html(response);
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.responseText);
                    $('#policeTable tbody').html(
                        `<tr><td colspan="9" class="text-danger text-center">Server error: ${xhr.status}</td></tr>`
                    );
                }
            });
        }

        // Bind status card click using delegation (works even for dynamically loaded cards)
        $(document).on('click', '.status-filter', function() {
            let status = $(this).data('status'); // "approved", "rejected", "pending", "all"
            let keyword = status === 'all' ? '' : status;

            // Set search input value (optional)
            $('#searchKeyword').val(keyword);

            // Call search API
            searchByKeyword(keyword);
        });

        // Bind keyup with debounce for search input
        $('#searchKeyword').on('keyup', debounce(function() {
            let keyword = $(this).val();
            searchByKeyword(keyword);
        }, 500));
    </script>
@endsection
