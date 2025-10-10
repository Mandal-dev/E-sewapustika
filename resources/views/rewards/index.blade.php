@extends('Dashboard.header')

@section('data')
@php
    $designation = Session::get('user.designation_type');
@endphp

<!-- Bootstrap + Custom CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="app-content p-3">

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ __('messages.success') }}:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ __('messages.error') }}:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($designation === 'Head_Person' || $designation === 'Rewards_Department')
        <div class="show-cards">
            <!-- Reward cards will be loaded via AJAX -->
        </div>
    @endif

    <br>

    <div class="table-section p-3" style="background: #fff; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.3);">

        <!-- Search Section -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div class="d-flex flex-wrap gap-2">
                <h5 class="mb-2 fw-semibold">{{ __('messages.reward_list') }}</h5>
            </div>

            <div class="search-container">
                <input type="text" id="searchKeyword" class="form-control ps-4"
                    placeholder="{{ __('messages.search_placeholder') }}">
                <i class="fas fa-search search-icon"></i>
            </div>
        </div>

        <div class="table-responsive" style="max-height:400px; overflow-y:auto; padding:10px;">
            <table class="table table-bordered align-middle my-rounded-table">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.serial') }}</th>
                        <th>{{ __('messages.officer_name') }}</th>
                        <th>{{ __('messages.buckle_number') }}</th>
                        <th>{{ __('messages.designation') }}</th>
                        <th>{{ __('messages.reward_date') }}</th>
                        <th>{{ __('messages.reward_type') }}</th>
                        <th>{{ __('messages.reward_reason') }}</th>
                        <th>{{ __('messages.document') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="rewardTableBody">
                    @include('rewards.table-rows', ['polices' => $polices])
                </tbody>
            </table>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="sewaPustikaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div id="sewaPustikaModalBody" class="p-4 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('messages.loading') }}...</span>
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
    // Auto fade alerts
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);

    // Fetch Rewards via AJAX - Modified to accept optional status parameter
    function fetchRewards(status = null) {
        let keyword = status !== null ? status : $("#searchKeyword").val();
        let designation = $("#searchDesignation").val();
        let isMobile = window.innerWidth < 768;

        // If status is provided via card click, update search input
        if (status !== null) {
            $("#searchKeyword").val(keyword);
        }

        $.ajax({
            url: "{{ route('rewards.search') }}",
            method: "GET",
            data: { keyword, designation },
            success: function(response) {
                if(isMobile){
                    $("#rewardCardsWrapper").html(response);
                } else {
                    $("#rewardTableBody").html(response);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                if(isMobile){
                    $("#rewardCardsWrapper").html('<div class="text-center text-danger">Failed to load records.</div>');
                } else {
                    $("#rewardTableBody").html('<tr><td colspan="10" class="text-center text-danger">Failed to load records.</td></tr>');
                }
            }
        });
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Search triggers - Remove duplicate bindings
    $("#searchDesignation").on("change", function() {
        fetchRewards();
    });

    $("#searchBtn").on("click", function() {
        fetchRewards();
    });

    $(window).resize(function() {
        fetchRewards();
    });

    // Single keyup event with debounce
    $('#searchKeyword').on('keyup', debounce(function() {
        fetchRewards();
    }, 500));

    // Status card click handler - Fixed
    $(document).on('click', '.status-filter', function() {
        let status = $(this).data('status'); // "approved", "rejected", "pending", "all"
        let keyword = status === 'all' ? '' : status;

        // Call fetchRewards with the status parameter
        fetchRewards(keyword);
    });

    // Modal open
    window.openModal = function(url) {
        const modal = new bootstrap.Modal(document.getElementById('sewaPustikaModal'));
        modal.show();
        $('#sewaPustikaModalBody').html('<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>');
        $.get(url, function(res) {
            $('#sewaPustikaModalBody').html(res);
        });
    }

    // Load reward cards initially for Head_Person / Rewards_Department
    @if($designation === 'Head_Person' || $designation === 'Rewards_Department')
    $.ajax({
        url: "{{ route('reward.cards') }}",
        type: "POST",
        data: { _token: "{{ csrf_token() }}" },
        success: function(response) {
            $('.show-cards').html(response);
        },
        error: function() {
            $('.show-cards').html('<p>{{ __('messages.failed_to_load') }}</p>');
        }
    });
    @endif
});
</script>
@endsection
