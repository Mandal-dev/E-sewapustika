@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">

    <!-- jQuery (required for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="app-content" >

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

        <!-- Search Section -->
        <div class="search-section p-3 d-flex flex-wrap align-items-center gap-2 mb-2"
            style="background: #fff; border-radius: 8px;">
            <input type="text" id="searchKeyword" class="form-control" placeholder="नाव, ठाणे किंवा बकल क्रमांक"
                style="min-width: 220px; flex: 1;">
            <select class="form-select" id="searchDesignation" style="width: 180px;">
                <option value="">सर्व ठाणे</option>
            </select>
            <button class="btn btn-success" id="searchButton">
                <i class="fas fa-search"></i> शोधा
            </button>
        </div>

        <!-- Table Section -->
        <div id="policeTable">
            @include('sewa_pustika.search_table', ['polices' => $polices])
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

    <!-- Scripts -->
    <script>
        $(document).ready(function() {
            const spinnerHtml = `
        <div class="p-5 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">लोड होत आहे...</span>
            </div>
        </div>
    `;

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 4000);

            // Open modal and load content via AJAX
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
                    error: function() {
                        $('#sewaPustikaModalBody').html(`
                    <div class="p-5 text-danger text-center">
                        डेटा लोड करण्यात अडचण आली.
                    </div>
                `);
                    }
                });
            }

            // Debounce function to limit AJAX calls on typing
            function debounce(func, delay) {
                let timeout;
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), delay);
                };
            }

            // AJAX search function
            function performSearch() {
                let keyword = $('#searchKeyword').val();
                let stationName = $('#searchDesignation').val(); // get selected station name

                $.ajax({
                    url: "{{ route('sevapustika.search') }}",
                    type: "GET",
                    data: {
                        keyword: keyword,
                        designation: stationName // send the station name as designation
                    },
                    success: function(response) {
                        $('#policeTable').html(response);
                    },
                    error: function() {
                        alert("डेटा लोड करण्यात अडचण आली");
                    }
                });
            }

            // Trigger search on typing (with debounce)
            $('#searchKeyword').on('input', debounce(performSearch, 500));

            // Trigger search on designation change
            $('#searchDesignation').change(performSearch);

            // Trigger search on search button click
            $('#searchButton').click(performSearch);

            // Handle pagination clicks via AJAX
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let keyword = $('#searchKeyword').val();
                let designation = $('#searchDesignation').val();

                $.ajax({
                    url: url,
                    data: {
                        keyword: keyword,
                        designation: designation
                    },
                    success: function(response) {
                        $('#policeTable').html(response);
                    }
                });
            });

            // Delegated event for modal edit buttons
            $(document).on('click', '.menuBtn', function() {
                let url = $(this).data('url');
                openModal(url);
            });
        });


        //station list

        $(document).ready(function() {
            $.get("{{ route('get.stations') }}", function(stations) {
                const select = $('#searchDesignation');
                select.empty();
                select.append('<option value="">सर्व ठाणे</option>');

                stations.forEach(name => {
                    select.append(`<option value="${name}">${name}</option>`);
                });
            });
        });
    </script>
@endsection
