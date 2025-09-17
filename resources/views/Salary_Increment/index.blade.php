@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery + Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="app-content" style="margin: 0; padding: 1rem;">

        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-2"
            style="background: #fff; padding: 1rem 1.5rem; border-radius: 8px;">
            <div class="breadcrumb d-flex align-items-center gap-2 mb-0">
                <i class="fas fa-home text-primary"></i>
                <span class="current fw-bold text-dark">वेतनवाढ</span>
                <span class="side-menu-text text-muted">मुख्य पृष्ठ</span>
            </div>
        </div>

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
            <input type="text" id="searchInput" class="form-control" placeholder="नाव, ठाणे किंवा बकल क्रमांक"
                style="min-width: 220px; flex: 1;" value="{{ $search ?? '' }}">
            <select id="designationFilter" class="form-select" style="width: 180px;">
                <option value="">सर्व वेतनवाढ जोडा</option>
                <option value="Police">पोलीस अधीक्षक</option>
                <option value="Inspector">निरीक्षक</option>
            </select>
            <button id="searchBtn" class="btn btn-success"><i class="fas fa-search"></i> शोधा</button>
        </div>

        <!-- Table Section -->
        <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
            <h5 class="mb-2 fw-semibold">वेतनवाढ यादी</h5>
            <p class="text-muted mb-3">एकूण नोंदी: {{ count($polices) }}</p>

            <div class="table-responsive ps-2" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>क्रमांक</th>
                            <th>पोलीस ठाणे</th>
                            <th>अधिकाऱ्याचे नाव</th>
                            <th>बकल क्रमांक</th>
                            <th>पद</th>
                            <th>वेतनवाढ दिनांक</th>
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
                        @include('salary_increment.table_rows', ['polices' => $polices])
                    </tbody>
                </table>
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

    <!-- AJAX + Search Script -->
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
@endsection
