@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery + Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@php
    $designation = Session::get('user.designation_type');
@endphp
    <div class="app-content">

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>यशस्वी:</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>चूक:</strong> {{ session('error') }}
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
                <h5 class="fw-semibold mb-0">वेतनवाढ यादी</h5>

                <div class="search-container position-relative" style="width: 300px;">
                    <input type="text" id="searchInput" class="form-control ps-4"
                        placeholder="नाव, ठाणे किंवा बकल क्रमांक">
                    <i class="fas fa-search search-icon position-absolute"></i>
                </div>
            </div>
            <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">
                        <tr>
                            <th>क्रमांक</th>
                            <th>Department</th>
                            <th>नाव</th>
                            <th>बकल क्रमांक</th>
                            <th>वेतनवाढ दिनांक</th>
                            <th>Present Days</th>
                            <th>वेतनवाढ प्रकार</th>
                            <th>Level</th>
                            <th>ग्रेड पेमेण्ट</th>
                            <th>नवीन वेतन</th>
                            <th>वाढलेली रक्कम</th>
                            <th>कागदपत्र</th>
                            <th>क्रिया</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        @php
                            $designation = Session::get('user.designation_type');
                        @endphp

                        @forelse($polices as $index => $police)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $police->post ?? '--' }}</td>
                                <td>{{ $police->police_name ?? '--' }}</td>
                                <td>{{ $police->buckle_number ?? '--' }}</td>
                                <td>{{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}
                                </td>
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
                                            <i class="fas fa-file-pdf"></i> पहा
                                        </a>
                                    @else
                                        <span class="text-muted">नाही</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Edit Icon -->
                                        @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                                            <button class="btn btn-primary btn-sm"
                                                onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')"
                                                title="Edit" style="padding: 6px 10px; border-radius: 50%;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        @endif
                                        @if ($designation === 'Head_Person' && $police->salary_increment_id && strtolower($police->salary_status) === 'pending')
                                            <button class="btn btn-sm btn-warning "
                                                style="padding: 6px 10px; border-radius: 50%;"
                                                onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')">
                                                <i class="fas fa-check me-1"></i>

                                            </button>
                                        @endif

                                        <!-- View Icon -->
                                        <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                            class="btn btn-info btn-sm" title="View"
                                            style="padding: 6px 10px; border-radius: 50%;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- Mobile Card View --}}
                            <div class="officer-card d-md-none p-3 mb-3 border rounded shadow-sm">
                                <div class="left-col mb-2">
                                    <p><strong>City:</strong> {{ $police->post ?? '--' }}</p>
                                    <p><strong>Police Name:</strong> {{ $police->police_name ?? '--' }}</p>
                                    <p><strong>Buckle No:</strong> {{ $police->buckle_number ?? '--' }}</p>
                                    <p><strong>Designation:</strong> {{ $police->designation_type ?? '--' }}</p>
                                    <p><strong>Increment Date:</strong>
                                        {{ $police->increment_date ? \Carbon\Carbon::parse($police->increment_date)->format('d-m-Y') : '--' }}
                                    </p>
                                    <p><strong>Increment Type:</strong> {{ $police->increment_type ?? '--' }}</p>
                                    <p><strong>Present Days:</strong> {{ $police->present_days ?? '--' }}</p>
                                </div>

                                <div class="right-col text-start mb-2">
                                    <div class="action-buttons">
                                        @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                                            <button class="add-btn btn-sm btn-warning mb-2"
                                                onclick="openModal('{{ route('salary_increment.add', $police->police_user_id) }}')">
                                                <i class="fas fa-plus"></i>Increment
                                            </button>
                                        @endif
                                        @if ($designation === 'Head_Person' && $police->salary_increment_id && strtolower($police->salary_status) === 'pending')
                                            <button class="btn btn-sm btn-warning "
                                                style="padding: 6px 10px; border-radius: 50%;"
                                                onclick="openModal('{{ route('salary.approval.show', $police->salary_increment_id) }}')">
                                                <i class="fas fa-check me-1"></i>

                                            </button>
                                        @endif
                                        <a class="view-btn btn-sm btn-info mb-2"
                                            href="{{ route('police_profile.index', $police->police_user_id) }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>

                                    <p><strong>Level:</strong> {{ $police->level ?? '--' }}</p>
                                    <p><strong>Grade Pay:</strong> {{ $police->grade_pay ?? '--' }}</p>
                                    <p><strong>New Salary:</strong> {{ $police->new_salary ?? '--' }}</p>
                                    <p><strong>Increased Amount:</strong> {{ $police->increased_amount ?? '--' }}</p>

                                    @if ($police->increment_documents)
                                        <a href="{{ route('salary_increment.view', $police->increment_documents) }}"
                                            target="_blank" class="btn btn-sm btn-danger mb-2">
                                            <i class="fas fa-file-pdf"></i> पहा
                                        </a>
                                    @else
                                        <p><span class="text-muted">नाही</span></p>
                                    @endif
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="15" class="text-center">कोणतीही नोंद सापडली नाही</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $polices->firstItem() }} to {{ $polices->lastItem() }}
                    of {{ $polices->total() }} records
                    (Page {{ $polices->currentPage() }} of {{ $polices->lastPage() }})
                </div>
                <div class="custom-pagination">
                    {!! $polices->links('pagination::bootstrap-5') !!}
                </div>
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
        });
    </script>
    <!-- jQuery (if not already included) -->

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
    </script>
@endsection
