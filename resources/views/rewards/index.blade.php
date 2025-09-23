@extends('Dashboard.header')

@section('data')
@php
    $designation = Session::get('user.designation_type');
@endphp
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- jQuery + Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="app-content" style="margin:0; padding:1rem;">

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
        @if ($designation === 'Head_Person' || $designation === 'Rewards_Department')
            <div class="show-cards">
                <!-- Reward cards will be loaded here via AJAX -->
            </div>
        @endif
        <br>
        <!-- Table Section (Desktop) -->
        <div class="table-section d-none d-md-block" id="rewardTableWrapper">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0">बक्षीस यादी</h5>

                <div class="search-container position-relative" style="width: 300px;">
                    <input type="text" id="searchKeyword" class="form-control ps-4"
                        placeholder="नाव, ठाणे किंवा बकल क्रमांक">
                    <i class="fas fa-search search-icon position-absolute"></i>
                </div>
            </div>

            <div class="table-responsive" style="max-height:400px; overflow-y:auto; padding:10px;">
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
                    <tbody id="rewardTableBody">
                        @include('rewards.table-rows', ['polices' => $polices])
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Section (Mobile) -->
        <div class="d-md-none" id="rewardCardsWrapper">
            @forelse($polices as $police)
                <div class="officer-card p-3 mb-3 border rounded shadow-sm">
                    <p><strong>नाव:</strong> {{ $police->police_name ?? '--' }}</p>
                    <p><strong>बकल नं.:</strong> {{ $police->buckle_number ?? '--' }}</p>
                    <p><strong>पद:</strong> {{ $police->role ?? '--' }}</p>
                    <p><strong>दिनांक:</strong>
                        {{ $police->reward_given_date ? \Carbon\Carbon::parse($police->reward_given_date)->format('d-m-Y') : '--' }}
                    </p>
                    <p><strong>प्रकार:</strong> {{ $police->reward_type ?? '--' }}</p>
                    <p><strong>कारण:</strong> {{ $police->reason ?? '--' }}</p>
                    <p>
                        <strong>स्थिती:</strong>
                        @if (strtolower($police->reward_status) === 'approved')
                            <span class="badge bg-success">मंजूर</span>
                        @elseif(strtolower($police->reward_status) === 'rejected')
                            <span class="badge bg-danger">नाकारले</span>
                        @else
                            <span class="badge bg-warning text-dark">प्रलंबित</span>
                        @endif
                    </p>
                </div>
            @empty
                <div class="text-center text-muted">कोणतीही नोंद सापडली नाही</div>
            @endforelse
        </div>

        <!-- Modal -->
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

        <!-- Pagination -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
            <div class="text-muted small"></div>
            <nav>{!! $polices->links('pagination::bootstrap-5') !!}</nav>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            setTimeout(() => $('.alert').fadeOut('slow'), 4000);

            function fetchRewards() {
                let keyword = $("#searchKeyword").val();
                let designation = $("#searchDesignation").val();
                let isMobile = window.innerWidth < 768;

                $.ajax({
                    url: "{{ route('rewards.search') }}",
                    method: "GET",
                    data: {
                        keyword,
                        designation
                    },
                    success: function(response) {
                        let items = response.data.data || response.data;
                        if (isMobile) {
                            // Mobile: update cards only
                            let cardHtml = '';
                            if (items.length > 0) {
                                items.forEach(item => {
                                    cardHtml += `
<div class="officer-card p-3 mb-3 border rounded shadow-sm">
    <p><strong>नाव:</strong> ${item.police_name ?? '--'}</p>
    <p><strong>बकल नं.:</strong> ${item.buckle_number ?? '--'}</p>
    <p><strong>पद:</strong> ${item.role ?? '--'}</p>
    <p><strong>दिनांक:</strong> ${item.reward_given_date ? new Date(item.reward_given_date).toLocaleDateString('en-GB') : '--'}</p>
    <p><strong>प्रकार:</strong> ${item.reward_type ?? '--'}</p>
    <p><strong>कारण:</strong> ${item.reason ?? '--'}</p>
    <p><strong>स्थिती:</strong> ${item.reward_status.toLowerCase() === 'approved' ? '<span class="badge bg-success">मंजूर</span>' : item.reward_status.toLowerCase() === 'rejected' ? '<span class="badge bg-danger">नाकारले</span>' : '<span class="badge bg-warning text-dark">प्रलंबित</span>'}</p>
</div>`;
                                });
                            } else {
                                cardHtml =
                                    `<div class="text-center text-muted">कोणतीही नोंद सापडली नाही</div>`;
                            }
                            $("#rewardCardsWrapper").html(cardHtml);
                        } else {
                            // Desktop: update table only
                            let tableHtml = '';
                            if (items.length > 0) {
                                items.forEach((item, index) => {
                                    tableHtml += `
<tr>
    <td>${response.data.from ? response.data.from + index : index + 1}</td>
    <td>${item.police_name ?? '--'}</td>
    <td>${item.buckle_number ?? '--'}</td>
    <td>${item.role ?? '--'}</td>
    <td>${item.reward_given_date ? new Date(item.reward_given_date).toLocaleDateString('en-GB') : '--'}</td>
    <td>${item.reward_type ?? '--'}</td>
    <td>${item.reason ?? '--'}</td>
    <td>${item.rewards_documents ? '<a href="/uploads/rewards/'+item.rewards_documents+'" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> पहा</a>' : '<span class="text-muted">नाही</span>'}</td>
    <td>${item.reward_status.toLowerCase() === 'approved' ? '<span class="badge bg-success">मंजूर</span>' : item.reward_status.toLowerCase() === 'rejected' ? '<span class="badge bg-danger">नाकारले</span>' : '<span class="badge bg-warning text-dark">प्रलंबित</span>'}</td>
    <td><button class="btn btn-sm btn-warning" onclick="openModal('/rewards/add/${item.police_user_id}')"><i class="fas fa-edit"></i> बक्षीस जोडा</button></td>
</tr>`;
                                });
                            } else {
                                tableHtml =
                                    `<tr><td colspan="10" class="text-center">कोणतीही नोंद सापडली नाही</td></tr>`;
                            }
                            $("#rewardTableBody").html(tableHtml);
                        }
                    }
                });
            }

            $("#searchKeyword").on("keyup", fetchRewards);
            $("#searchDesignation").on("change", fetchRewards);
            $("#searchBtn").on("click", fetchRewards);

            // Optional: re-fetch on window resize
            $(window).resize(function() {
                fetchRewards();
            });
        });

        // Modal open
        function openModal(url) {
            const modal = new bootstrap.Modal(document.getElementById('sewaPustikaModal'));
            modal.show();
            $('#sewaPustikaModalBody').html(
                '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>');
            $.get(url, function(res) {
                $('#sewaPustikaModalBody').html(res);
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('reward.cards') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}" // Include CSRF token for POST
                },
                success: function(response) {
                    // Inject the returned view HTML into the div
                    $('.show-cards').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $('.show-cards').html('<p>Failed to load reward cards.</p>');
                }
            });
        });
    </script>
@endsection
