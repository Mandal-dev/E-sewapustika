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

    @php
        $designation = Session::get('user.designation_type');
    @endphp

    <div class="app-content">

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ __('messages.success') }}:</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ __('messages.error') }}:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($designation === 'Head_Person' || $designation === 'Account_Department')
            <div class="show-cards">
                <!-- Salary increment cards will load here via AJAX -->
            </div>
            <br>
        @endif

        <!-- Table Section -->
        <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0">{{ __('messages.salary_increment_list') }}</h5>

                <div class="search-container position-relative" style="width: 300px;">
                    <input type="text" id="searchInput" class="form-control ps-4"
                        placeholder="{{ __('messages.search_placeholder') }}">
                    <i class="fas fa-search search-icon position-absolute"></i>
                </div>
            </div>

            <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.sr_no') }}</th>
                            <th>{{ __('messages.department') }}</th>
                            <th>{{ __('messages.police_name') }}</th>
                            <th>{{ __('messages.buckle_number') }}</th>
                            <th>{{ __('messages.increment_date') }}</th>
                            <th>{{ __('messages.present_days') }}</th>
                            <th>{{ __('messages.increment_type') }}</th>
                            <th>{{ __('messages.level_no') }}</th>
                            <th>{{ __('messages.grade_pay') }}</th>
                            <th>{{ __('messages.net_salary') }}</th>
                            <th>{{ __('messages.increased_amount') }}</th>
                            <th>{{ __('messages.increment_documents') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @forelse($polices as $index => $police)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $police->police_station_name ?? '--' }}</td>
                                <td>{{ $police->police_name ?? '--' }}</td>
                                <td>{{ $police->buckle_number ?? '--' }}</td>
                                <td>{{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}</td>
                                <td>{{ $police->present_days ?? '--' }}</td>
                                <td>{{ $police->increment_type ?? '--' }}</td>
                                <td>{{ $police->level ?? '--' }}</td>
                                <td>{{ $police->grade_pay ?? '--' }}</td>
                                <td>{{ $police->new_salary ?? '--' }}</td>
                                <td>{{ $police->increased_amount ?? '--' }}</td>
                                <td>
                                    @if ($police->increment_documents)
                                        <a href="{{ route('salary_increment.view', $police->increment_documents) }}"
                                            target="_blank" class="btn btn-sm btn-danger">
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
                                            data-label="{{ __('messages.remark') }}:"
                                            data-title="{{ __('messages.approved') }}">
                                            {{ __('messages.approved') }}
                                        </span>
                                    @elseif ($status === 'rejected')
                                        <span class="badge bg-danger text-white status-badge" style="cursor:pointer"
                                            data-variable="{{ $police->remark ?? __('messages.not_available') }}"
                                            data-label="{{ __('messages.reject_reason') }}:"
                                            data-title="{{ __('messages.rejected') }}">
                                            {{ __('messages.rejected') }}
                                        </span>
                                    @elseif ($status === 'uploaded')
                                        <span class="badge bg-info text-white status-badge" style="cursor:pointer"
                                            data-variable="{{ __('messages.uploaded_pending_review') }}"
                                            data-label="{{ __('messages.status') }}:"
                                            data-title="{{ __('messages.uploaded') }}">
                                            {{ __('messages.uploaded') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark status-badge" style="cursor:pointer"
                                            data-variable="{{ __('messages.pending') }}"
                                            data-label="{{ __('messages.status') }}:"
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
                                                title="{{ __('messages.add_increment') }}"
                                                style="padding: 6px 10px; border-radius: 50%;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                            class="btn btn-info btn-sm"
                                            title="{{ __('messages.view_profile') }}"
                                            style="padding: 6px 10px; border-radius: 50%;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if ($designation === 'Head_Person' && $police->salary_increment_id && $status === 'pending')
                                            <button class="btn btn-sm btn-warning"
                                                style="padding: 6px 10px; border-radius: 50%;"
                                                onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')">
                                                <i class="fas fa-check me-1"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Mobile Card View --}}
                            <div class="officer-card d-md-none p-3 mb-3 border rounded shadow-sm">
                                <div class="left-col mb-2">
                                    <p><strong>{{ __('messages.department') }}:</strong> {{ $police->increment_documents ?? '--' }}</p>
                                    <p><strong>{{ __('messages.police_name') }}:</strong> {{ $police->police_name ?? '--' }}</p>
                                    <p><strong>{{ __('messages.buckle_number') }}:</strong> {{ $police->buckle_number ?? '--' }}</p>
                                    <p><strong>{{ __('messages.designation') }}:</strong> {{ $police->designation_type ?? '--' }}</p>
                                    <p><strong>{{ __('messages.increment_date') }}:</strong> {{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}</p>
                                    <p><strong>{{ __('messages.increment_type') }}:</strong> {{ $police->increment_type ?? '--' }}</p>
                                    <p><strong>{{ __('messages.present_days') }}:</strong> {{ $police->present_days ?? '--' }}</p>
                                </div>

                                <div class="right-col text-start mb-2">
                                    <p><strong>{{ __('messages.level_no') }}:</strong> {{ $police->level ?? '--' }}</p>
                                    <p><strong>{{ __('messages.grade_pay') }}:</strong> {{ $police->grade_pay ?? '--' }}</p>
                                    <p><strong>{{ __('messages.net_salary') }}:</strong> {{ $police->new_salary ?? '--' }}</p>
                                    <p><strong>{{ __('messages.increased_amount') }}:</strong> {{ $police->increased_amount ?? '--' }}</p>

                                    @if ($police->increment_documents)
                                        <a href="{{ route('salary_increment.view', $police->increment_documents) }}"
                                            target="_blank" class="btn btn-sm btn-danger mb-2">
                                            <i class="fas fa-file-pdf"></i> {{ __('messages.view') }}
                                        </a>
                                    @else
                                        <p><span class="text-muted">{{ __('messages.no_document') }}</span></p>
                                    @endif

                                    <div class="action-buttons">
                                        @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                                            <button class="add-btn btn-sm btn-warning mb-2"
                                                onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')">
                                                <i class="fas fa-plus"></i> {{ __('messages.add_increment') }}
                                            </button>
                                        @endif
                                        @if ($designation === 'Head_Person' && $police->salary_increment_id && $status === 'pending')
                                            <button class="btn btn-sm btn-warning"
                                                onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')">
                                                <i class="fas fa-check me-1"></i> {{ __('messages.approve') }}
                                            </button>
                                        @endif
                                        <a class="view-btn btn-sm btn-info mb-2"
                                            href="{{ route('police_profile.index', $police->police_user_id) }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="15" class="text-center">{{ __('messages.no_records_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">

                </div>
                <div class="custom-pagination">
                    {!! $polices->links('pagination::bootstrap-5') !!}
                </div>
            </div>
                    <!-- Bootstrap Modal -->
        <div class="modal fade" id="sewaPustikaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div id="sewaPustikaModalBody" class="p-4 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">लोड होत आहे...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject/Status Modal -->
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
        </div>

    <!-- AJAX + Search + Validation Script -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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

                    // Add Present Days Validation for modal form
                    $('#incrementSubmit').click(function(e) {
                        const presentDays = parseInt($('#present_days').val());
                        if (isNaN(presentDays) || presentDays < 180) {
                            e.preventDefault();
                            alert(
                                "वेतनवाढसाठी उपस्थित दिवस कमीत कमी 180 असणे आवश्यक आहे.\nAttendance is too low for salary increment."
                            );
                            return false;
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error, xhr.responseText);
                    let message = "डेटा लोड करण्यात अडचण आली.";
                    if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    }
                    $('#sewaPustikaModalBody').html(`
                        <div class="p-5 text-danger text-center">${message}</div>
                    `);
                }
            });
        }

        $(document).ready(function() {
            setTimeout(() => $('.alert').fadeOut('slow'), 4000);

            function performSearch() {
                let search = $('#searchInput').val();
                let designation = $('#designationFilter').val();

                $.ajax({
                    url: "{{ route('SalaryIncrement.search') }}",
                    type: "GET",
                    data: {
                        search,
                        designation
                    },
                    success: function(data) {
                        $('#tableBody').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Search AJAX error:', error);
                    }
                });
            }

            $('#searchInput').on('keyup', performSearch);
            $('#searchBtn').on('click', performSearch);
            // Status card click filter
            $(document).on('click', '.status-filter', function() {
                const status = $(this).data('status');
                const keyword = status === 'all' ? '' : status;
                $('#searchInput').val(keyword);
                performSearch();
            });
        });
    </script>
    <!-- jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // AJAX call to load salary increment cards
            $.ajax({
                url: "{{ route('salary.increment.cards') }}", // route defined in web.php
                type: "GET",
                success: function(response) {
                    // Inject returned HTML into the div
                    $('.show-cards').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error loading salary cards:", error);
                    $('.show-cards').html(
                        '<p class="text-danger">Unable to load salary increment cards.</p>');
                }
            });
        });
        $(document).on('click', '.status-filter', function() {
            const status = $(this).data('status'); // approved, rejected, uploaded, all

            // Highlight the active card (optional)
            $('.status-filter').removeClass('active-card');
            $(this).addClass('active-card');

            // Call your search route directly with the status
            $.ajax({
                url: "{{ route('SalaryIncrement.search') }}",
                type: "GET",
                data: {
                    keyword: status
                }, // send the clicked word
                success: function(data) {
                    $('#tableBody').html(data); // update table
                },
                error: function(xhr, statusText, error) {
                    console.error('Search AJAX error:', error);
                }
            });
        });

        $(document).on('click', '.status-badge', function() {
            const title = $(this).data('title');
            const label = $(this).data('label');
            const variable = $(this).data('variable');

            const modal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
            modal.show();

            $('#rejectReasonModalTitle').text(title);

            let color = 'black';
            if (title.toLowerCase().includes('मंजूर')) color = 'green';
            else if (title.toLowerCase().includes('नाकारले')) color = 'red';
            else if (title.toLowerCase().includes('प्रलंबित')) color = 'orange';
            else if (title.toLowerCase().includes('अपलोड')) color = 'blue';

            $('#rejectReasonModalBody').html(
                `<p>${label} <span style="color:${color}; font-weight:bold;">${variable}</span></p>`
            );
        });
    </script>
@endsection
