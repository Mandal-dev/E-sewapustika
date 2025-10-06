@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @php
        $designation = Session::get('user.designation_type');
    @endphp

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

        <!-- Reward Cards (if Head/Sewapustika) -->
        @if ($designation === 'Head_Person' || $designation === 'Sewapustika_Department')
            <div class="show-cards"></div>
        @endif

        <div class="table-section p-3"
             style="background: #fff; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.3);">

            <!-- Search & Buttons -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div class="d-flex flex-wrap gap-2">
                    <a class="section-btn1" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                        <i class="fas fa-plus"></i> {{ __('messages.add_police') }}
                    </a>
                    <a href="{{ route('police-users.template') }}" class="section-btn2">
                        <i class="fas fa-file-excel"></i> {{ __('messages.download_template') }}
                    </a>
                </div>

                <div class="search-container">
                    <input type="text" id="searchInput" placeholder="{{ __('messages.search_placeholder') }}">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-2 fw-semibold">{{ __('messages.police_list') }}</h5>
                <div class="add-officer-btn" onclick="openModal('{{ route('police.create') }}')">
                    <i class="fas fa-plus-circle"></i> {{ __('messages.add_officer') }}
                </div>
            </div>

            <!-- Police Table -->
            <div id="policeTable">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('messages.loading') }}</span>
                    </div>
                </div>
            </div>

            <!-- Modal for Create/Edit -->
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

            <!-- Modal for Excel Upload -->
            <div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('import.police.users') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('messages.upload_excel') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                                <small class="text-muted">{{ __('messages.upload_template_note') }}</small>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">{{ __('messages.upload') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Auto-hide alerts
            setTimeout(() => $('.alert').fadeOut('slow'), 4000);

            // Open Modal Function
            window.openModal = function(url) {
                const modalElement = document.getElementById('sewaPustikaModal');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();

                $('#sewaPustikaModalBody').html(`
                    <div class="p-5 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('messages.loading') }}</span>
                        </div>
                    </div>
                `);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#sewaPustikaModalBody').html(response);
                    },
                    error: function() {
                        $('#sewaPustikaModalBody').html(`
                            <div class="p-5 text-danger text-center">
                                {{ __('messages.load_error') }}
                            </div>
                        `);
                    }
                });
            }

            // Load police users table
            function loadTable(query = '', designation = '') {
                $.ajax({
                    url: query ? "{{ route('police_users.search_table') }}" :
                        "{{ route('police_users.list.table') }}",
                    method: "GET",
                    data: {
                        search: query,
                        designation: designation
                    },
                    success: function(response) {
                        $('#policeTable').html(response);
                    },
                    error: function(xhr) {
                        console.error("Error loading table:", xhr.responseText);
                    }
                });
            }

            // Initial load
            loadTable();

            // Debounced search
            let debounceTimer;
            $('#searchInput, #designationFilter').on('input change', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    let query = $('#searchInput').val();
                    let designation = $('#designationFilter').val();
                    loadTable(query, designation);
                }, 300);
            });

            // Search button click
            $('#searchBtn').on('click', function() {
                let query = $('#searchInput').val();
                let designation = $('#designationFilter').val();
                loadTable(query, designation);
            });

            // Load reward cards
            $.ajax({
                url: "{{ route('dashboard.cards') }}",
                data: { _token: "{{ csrf_token() }}" },
                success: function(response) {
                    $('.show-cards').html(response);
                },
                error: function() {
                    $('.show-cards').html('<p>{{ __('messages.load_error') }}</p>');
                }
            });
        });
    </script>
@endsection
