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

<div class="app-content">
    @php
        $designation = Session::get('user.designation_type');
    @endphp

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

    <!-- Dashboard Cards -->
    @if ($designation === 'Head_Person' || $designation === 'Sewapustika_Department')
        <div class="show-cards">
            <!-- Cards loaded via AJAX -->
        </div>
    @endif

    <br>

    <!-- Search Section -->
    <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-semibold mb-0">{{ __('messages.sewa_pustika') }}</h5>
            <div class="search-container position-relative" style="width: 300px;">
                <input type="text" id="searchKeyword" class="form-control ps-4"
                    placeholder="{{ __('messages.name') }}, {{ __('messages.station') }} किंवा {{ __('messages.buckle_number') }}">
                <i class="fas fa-search search-icon position-absolute"></i>
            </div>
        </div>

        <!-- Table Section -->
        <div id="policeTable">
            @include('sewa_pustika.search_table', ['polices' => $polices])
        </div>

        <!-- Sewa Pustika Modal -->
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

        <!-- Reject/Status Modal -->
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
    </div> <!-- table-section -->
</div> <!-- app-content -->



<!-- AJAX + JS -->
<script>
$(document).ready(function() {
    const spinnerHtml = `
        <div class="p-5 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('messages.loading') }}</span>
            </div>
        </div>
    `;

    // Auto-hide flash alerts
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);

    // AJAX setup with CSRF token
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Debounce function
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Perform AJAX search
    function performSearch() {
        const keyword = $('#searchKeyword').val();
        const stationName = $('#searchDesignation').val();

        $('#policeTable').html(spinnerHtml);

        $.ajax({
            url: "{{ route('sevapustika.search') }}",
            type: 'GET',
            data: { keyword, designation: stationName },
            success: function(response) {
                $('#policeTable').html(response);
            },
            error: function() {
                $('#policeTable').html(`
                    <div class="alert alert-danger text-center">
                        डेटा लोड करण्यात अडचण आली. कृपया पुन्हा प्रयत्न करा.
                    </div>
                `);
            }
        });
    }

    // Open Sewa Pustika modal
    function openModal(url) {
        const modalElement = document.getElementById('sewaPustikaModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        $('#sewaPustikaModalBody').html(spinnerHtml);

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#sewaPustikaModalBody').html(response);
            },
            error: function(xhr) {
                let message = "{{ __('messages.loading') }}";
                if(xhr.status === 403 && xhr.responseJSON?.error) message = xhr.responseJSON.error;
                $('#sewaPustikaModalBody').html(
                    `<div class="p-5 text-danger text-center">${message}</div>`
                );
            }
        });
    }

    // Open Reject/Status Modal for badges
    $(document).on('click', '.status-badge', function() {
        const title = $(this).data('title');
        const label = $(this).data('label');
        const variable = $(this).data('variable');

        const modal = new bootstrap.Modal(document.getElementById('rejectReasonModal'));
        modal.show();

        $('#rejectReasonModalTitle').text(title);

        let color = 'black';
        if(title.toLowerCase().includes('मंजूर')) color = 'green';
        else if(title.toLowerCase().includes('नाकारले')) color = 'red';
        else if(title.toLowerCase().includes('प्रलंबित')) color = 'orange';
        else if(title.toLowerCase().includes('अपलोड')) color = 'blue';

        $('#rejectReasonModalBody').html(
            `<p>${label} <span style="color:${color}; font-weight:bold;">${variable}</span></p>`
        );
    });

    // Load Dashboard Cards via AJAX
    $.ajax({
        url: "{{ route('sewa.pustika.cards') }}",
        data: { _token: "{{ csrf_token() }}" },
        success: function(response) {
            $('.show-cards').html(response);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            $('.show-cards').html('<p>{{ __('messages.loading') }}</p>');
        }
    });

    // Event bindings
    $('#searchKeyword').on('input', debounce(performSearch, 5000));
    $('#searchDesignation').change(performSearch);
    $('#searchButton').click(performSearch);

    // Status card click filter
    $(document).on('click', '.status-filter', function() {
        const status = $(this).data('status');
        const keyword = status === 'all' ? '' : status;
        $('#searchKeyword').val(keyword);
        performSearch();
    });

    // Open modal on dynamic buttons
    $(document).on('click', '.menuBtn', function(e) {
        e.preventDefault();
        openModal($(this).data('url'));
    });
});
</script>
@endsection
