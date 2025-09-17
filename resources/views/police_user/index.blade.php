@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">

    <!-- jQuery (only if not already loaded in layout) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (only if not already loaded in layout) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="app-content p-3">
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center mb-2"
            style="background: #ffffffcc; padding: 1rem 1.5rem; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);">

            <div class="breadcrumb d-flex align-items-center gap-2 mb-0">
                <i class="fas fa-home text-primary"></i>
                <span class="current fw-bold text-dark">सर्व पोलीस</span>
                <span class="side-menu-text text-muted">मुख्य पृष्ठ</span>
            </div>

            <div class="d-flex gap-2">
                <!-- Download Template -->
                <a href="{{ route('police-users.template') }}" class="btn btn-outline-success">
                    <i class="fas fa-file-excel"></i> टेम्पलेट डाउनलोड
                </a>

                <!-- Upload Excel -->
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadExcelModal">
                    <i class="fas fa-upload"></i> एक्सेल अपलोड
                </button>

                <!-- Add Police -->
                <button class="btn btn-primary" onclick="openModal('{{ route('police.create') }}')">
                    <i class="fas fa-plus"></i> पोलीस जोडा
                </button>
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
            style="background: #fff; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.3)">
            <input type="text" id="searchInput" class="form-control" placeholder="नाव, ठाणे किंवा बकल क्रमांक"
                style="min-width: 220px; flex: 1;">
            <select class="form-select" id="designationFilter" style="width: 180px;">
                <option value="">सर्व पोलीस</option>
                <option value="पोलीस अधीक्षक">पोलीस अधीक्षक</option>
                <option value="निरीक्षक">निरीक्षक</option>
            </select>
            <button class="btn btn-success" id="searchBtn"><i class="fas fa-search"></i> शोधा</button>
        </div>

        <!-- Police Users Table -->
        <div id="policeTable">
            <div class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">लोड होत आहे...</span>
                </div>
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

        <!-- Modal for Excel Upload -->
        <div class="modal fade" id="uploadExcelModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('import.police.users') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">एक्सेल अपलोड</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">कृपया <b>टेम्पलेट</b> वापरूनच डेटा अपलोड करा.</small>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">अपलोड करा</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

<script>
$(document).ready(function() {
    // Auto-hide alerts
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);

    // Modal open function
    window.openModal = function(url) {
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
            error: function(xhr) {
                $('#sewaPustikaModalBody').html(`
                    <div class="p-5 text-danger text-center">
                        डेटा लोड करण्यात अडचण आली.
                    </div>
                `);
            }
        });
    }

    // Load police users table
    function loadTable(query = '', designation = '') {
        $.ajax({
            url: query ? "{{ route('police_users.search_table') }}" : "{{ route('police_users.list.table') }}",
            method: "GET",
            data: { search: query, designation: designation },
            success: function(response) {
                $('#policeTable').html(response);
                console.log("Table loaded/updated");
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
});
</script>

@endsection
