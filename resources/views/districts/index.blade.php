@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">

    <!-- jQuery (required for AJAX) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle (Modal needs this) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- App Content -->
    <div class="app-content" style="margin: 0; padding: 1rem;">

        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-2"
            style="background: #fff; padding: 1rem 1.5rem; border-radius: 8px;">
            <div class="breadcrumb d-flex align-items-center gap-2 mb-0">
                <i class="fas fa-home text-primary"></i>
                <span class="current fw-bold text-dark">सर्व जिल्हा</span>
                <span class="side-menu-text text-muted">मुख्य पृष्ठ</span>
            </div>
            <button class="btn btn-primary" onclick="openModal('{{ route('districts.create') }}')">
                <i class="fas fa-plus"></i> जिल्हा जोडा
            </button>
        </div>

        <!-- ✅ Flash Messages -->
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
            <input type="text" class="form-control" placeholder="नाव, ठाणे किंवा बकल क्रमांक"
                style="min-width: 220px; flex: 1;">
            <select class="form-select" style="width: 180px;">
                <option>सर्व जिल्हा</option>
                <option>पोलीस अधीक्षक</option>
                <option>निरीक्षक</option>
            </select>
            <button class="btn btn-success"><i class="fas fa-search"></i> शोधा</button>
        </div>

        <!-- Table Section -->
        <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
            <h5 class="mb-2 fw-semibold"> जिल्हा यादी</h5>
            <p class="text-muted mb-3">एकूण {{ count($districts) }} नोंदी</p>

            <div class="table-responsive ps-2" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>क्रमांक</th>
                            <th>राज्याचे नाव</th>
                            <th>जिल्ह्याचे नाव</th>
                            <th>स्थिती</th>
                            <th>क्रिया</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($districts as $key => $district)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $district->state_name ?? 'N/A' }}</td>
                                <td>{{ $district->district_name }}</td>
                                <td>
                                    <span class="badge {{ $district->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $district->status }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning d-flex align-items-center gap-1"
                                        onclick="openModal('{{ route('districts.edit', $district->id) }}')">
                                        <i class="fas fa-edit"></i> संपादन
                                    </button>
                                </td>
                            </tr>

                            <!-- Mobile Card View -->
                            <div class="officer-card d-md-none">
                                <div class="left-col">
                                    <p class="department"><strong>State Name:</strong>{{ $district->state_name ?? 'N/A' }}
                                    </p>
                                    <p><strong>District Name:</strong>{{ $district->district_name }}</p>

                                </div>


                                <div class="right-col text-start">

                                <button class="action-btn"
                                        onclick="openModal('{{ route('districts.edit', $district->id) }}')">
                                        <i class="fas fa-edit"></i> संपादन
                                    </button>

                                    <p><span
                                            class="badge {{ $district->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $district->status }}
                                        </span></p>


                                </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal for Create/Edit -->
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

        // ✅ Auto-hide flash alerts after 4 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 4000);
        });
    </script>
@endsection
