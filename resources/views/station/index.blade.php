@extends('Dashboard.header')

@section('data')
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- App Content -->
    <div class="app-content" style="margin: 0; padding: 1rem;">

        <!-- ✅ Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>यशस्वी:</strong> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>चूक:</strong> {{ session('error') }}
            </div>
        @endif

        <!-- 🔍 Search Section -->
        <div class="card p-4 mb-3">
            <div class="gapp d-flex">
                <div class="search-container">
                    <input type="text" id="searchInput" class="form-control" placeholder="नाव, ठाणे किंवा बकल क्रमांक">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>

            <!-- Header -->
            <div class="page-header d-flex align-items-center gap-2 mb-3"
                style="background: #fff; padding: 1rem 1.5rem; border-radius: 8px; justify-content: space-between;">
                <div class="breadcrumb d-flex align-items-center gap-2 mb-0">
                    <span><b>Station</b></span>
                </div>
                <span style="text-align: center;">
                    <a onclick="openModal('{{ route('stations.create') }}')" class="btn-txt"
                        style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer; text-decoration: none;">
                        <i class="fas fa-plus-circle" style="font-size:20px; color:#133367;"></i>
                        <span style="text-decoration: underline; color: inherit;">Add विभाग</span>
                    </a>
                </span>
            </div>

            <!-- ✅ Station Table -->
            <div class="table-section p-3 mb-3 card1"
                style="background: #fff; border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">

                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">
                        <tr>
                            <th class="my-cell">#</th>
                            <th class="my-cell">Country</th>
                            <th class="my-cell">Division</th>
                            <th class="my-cell">City</th>
                            <th class="my-cell">विभाग</th>
                            <th class="my-cell">Status</th>
                            <th class="my-cell">Action</th>
                        </tr>
                    </thead>
                    <!-- ✅ Added ID here -->
                    <tbody id="stationTable">
                        @forelse ($stations as $key => $station)
                            <tr>
                                <td class="my-cell1">{{ $key + 1 }}</td>
                                <td class="my-cell1">{{ $station->state_name ?? 'N/A' }}</td>
                                <td class="my-cell">{{ $station->district_name ?? 'N/A' }}</td>
                                <td class="my-cell">{{ $station->city_name ?? 'N/A' }}</td>
                                <td class="my-cell">{{ $station->station_name ?? 'N/A' }}</td>
                                <td class="my-cell">
                                    <span class="{{ $station->status == 'Active' ? 'text-success fw-bold' : 'text-danger' }}">
                                        @if ($station->status == 'Active')
                                            <span class="status-dot"></span> {{ $station->status }}
                                        @else
                                            {{ $station->status }}
                                        @endif
                                    </span>
                                </td>
                                <td class="my-cell">
                                    <button class="cus-btn">
                                        <i class="fas fa-edit"></i> संपादन
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">नोंदी आढळल्या नाहीत</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="sewaPustikaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background: #FFE0b3;">
                    <h5 class="modal-title fw-bold">ठाणे जोडा</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="sewaPustikaModalBody" class="p-4 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">लोड होत आहे...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        $(document).ready(function() {
            // ✅ Auto-hide alerts
            setTimeout(() => $('.alert').fadeOut('slow'), 4000);

            // ✅ Live search
            $('#searchInput').on('keyup', function() {
                let query = $(this).val();

                $.ajax({
                    url: "{{ route('stations.search') }}",
                    method: "GET",
                    data: { search: query },
                    success: function(response) {
                        // Replace only tbody content
                        $('#stationTable').html($(response).find('#stationTable').html());
                    },
                    error: function(xhr) {
                        console.error("Search error:", xhr.responseText);
                    }
                });
            });
        });

        // ✅ Modal open function
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
    </script>

    <!-- Inline CSS -->
    <style>
        .cus-btn {
            background: #4db1d3;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 11px;
            cursor: pointer;
        }
        .cus-btn i {
            margin-right: 5px;
        }
    </style>
@endsection
