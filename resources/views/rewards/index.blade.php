@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">

    <!-- jQuery (required for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="app-content" style="margin: 0; padding: 1rem;">



        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>यशस्वी:</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>चूक:</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search Section -->
 <div class="search-section p-3 d-flex flex-wrap align-items-center gap-2 mb-2"
    style="background: #fff; border-radius: 8px;">
    <input type="text" id="searchKeyword" class="form-control"
           placeholder="नाव, ठाणे किंवा बकल क्रमांक"
           style="min-width: 220px; flex: 1;">

    <select id="searchDesignation" class="form-select" style="width: 180px;">
        <option value="">सर्व बक्षीस जोडा</option>
        <option value="Police">पोलीस</option>
        <option value="Station_Head">स्टेशन हेड</option>
        <option value="Head_Person">हेड पर्सन</option>
        <option value="Admin">ॲडमिन</option>
    </select>

    <button class="btn btn-success" id="searchBtn"><i class="fas fa-search"></i> शोधा</button>
</div>


        <!-- Table Section -->
        <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
            <h5 class="mb-2 fw-semibold">बक्षीस यादी</h5>
            <p class="text-muted mb-3"></p>

            <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">
                        <tr>
                            <th>क्रमांक</th>
                            <th>अधिकाऱ्याचे नाव</th>
                            <th>बकल क्रमांक</th>
                            <th>पद</th>
                            <th>बक्षीस दिनांक</th>
                            <th>बक्षिसांचे प्रकार</th>
                            <th>बक्षिसांचे कारण</th>
                            <th>कागदपत्र</th>
                            <th>स्थिती</th>
                            <th>क्रिया</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polices as $index => $police)
                            <tr>
                                <td>{{ $polices->firstItem() + $index }}</td>
                                <td>{{ $police->police_name ?? '--' }}</td>
                                <td>{{ $police->buckle_number ?? '--' }}</td>
                                <td>{{ $police->role ?? '--' }}</td>
                                <td>
                                    {{ $police->reward_given_date ? \Carbon\Carbon::parse($police->reward_given_date)->format('d-m-Y') : '--' }}
                                </td>
                                <td>{{ $police->reward_type ?? '--' }}</td>
                                <td>{{ $police->reason ?? '--' }}</td>
                                <td>
                                    @if ($police->rewards_documents)
                                        <a href="{{ asset('uploads/rewards/' . $police->rewards_documents) }}"
                                            target="_blank" class="btn btn-sm btn-danger">
                                            <i class="fas fa-file-pdf"></i> पहा
                                        </a>
                                    @else
                                        <span class="text-muted">नाही</span>
                                    @endif
                                </td>
                                <td>
                                    @if (strtolower($police->reward_status) === 'approved')
                                        <span class="badge bg-success">मंजूर</span>
                                    @elseif (strtolower($police->reward_status) === 'rejected')
                                        <span class="badge bg-danger">नाकारले</span>
                                    @else
                                        <span class="badge bg-warning text-dark">प्रलंबित</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Add Reward -->
                                    <button class="btn btn-sm btn-warning"
                                        onclick="openModal('{{ route('rewards.add', $police->police_user_id) }}')">
                                        <i class="fas fa-edit"></i> बक्षीस जोडा
                                    </button>

                                    @if ($police->reward_id)
                                        @if (strtolower($police->reward_status) === 'pending')
                                            <!-- Approve -->
                                            <button class="btn btn-sm btn-success"
                                                onclick="aproveopenModal('{{ route('aprove.rewards.show', $police->reward_id) }}')">
                                                <i class="fas fa-check-circle"></i> मंजूर करा
                                            </button>
                                        @elseif(strtolower($police->reward_status) === 'rejected')
                                            <!-- View Reject Reason -->
                                            <button class="btn btn-sm btn-danger"
                                                onclick="viewRejectReason('{{ $police->reason ?? 'No reason provided' }}')">
                                                <i class="fas fa-eye"></i> कारण पहा
                                            </button>
                                        @endif
                                    @endif

                                    <!-- View Profile -->
                                    <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">कोणतीही नोंद सापडली नाही</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
<div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
    <!-- Left: Records Info -->
    <div class="text-muted small">
        दर्शवित आहे <strong>{{ $polices->firstItem() }}</strong> ते
        <strong>{{ $polices->lastItem() }}</strong> पैकी
        <strong>{{ $polices->total() }}</strong> नोंदी
        <span class="ms-2">(पान {{ $polices->currentPage() }} / {{ $polices->lastPage() }})</span>
    </div>

    <!-- Right: Pagination Links -->
    <nav>
        {!! $polices->links('pagination::bootstrap-5') !!}
    </nav>
</div>


        </div>

        <!-- Modals -->
        <!-- Add/Edit Reward -->
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

        <!-- Approve Reward Modal -->
        <div class="modal fade" id="aprovePustikaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 489px; height: 706px;">
                <div class="modal-content text-center">
                    <div id="aproveModalBody" class="p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">लोड होत आहे...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Reason Modal -->
        <div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-4 text-center">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Reason</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="rejectReasonBody">
                        <!-- Reason will be inserted here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script>
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

        function aproveopenModal(url) {
            const modalElement = document.getElementById('aprovePustikaModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            $('#aproveModalBody').html(`
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
                    $('#aproveModalBody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error, xhr.responseText);
                    $('#aproveModalBody').html(`
                <div class="p-5 text-danger text-center">
                    डेटा लोड करण्यात अडचण आली.
                </div>
            `);
                }
            });
        }

        function viewRejectReason(reason) {
            const modalBody = document.getElementById('rejectReasonBody');
            modalBody.textContent = reason;
            const modal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
            modal.show();
        }

        // Auto-hide alerts
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 4000);
        });
    </script>

@endsection
