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
        @php
            $designation = Session::get('user.designation_type');
        @endphp

        <!-- Header -->


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
                <option>सर्व शिक्षा जोडा</option>
                <option>पोलीस अधीक्षक</option>
                <option>निरीक्षक</option>
            </select>
            <button class="btn btn-success"><i class="fas fa-search"></i> शोधा</button>
        </div>

        <!-- Table Section -->
        <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
            <h5 class="mb-2 fw-semibold">शिक्षा यादी</h5>


            <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">

                        <tr>
                            <th>क्रमांक</th>
                            <th>अधिकाऱ्याचे नाव</th>
                            <th>बकल क्रमांक</th>
                            <th>शिक्षेची तारीख</th>
                            <th>शिक्षेचे प्रकार</th>
                            <th>शिक्षेचे कारण</th>
                            <th>दस्तऐवज</th>
                            <th>क्रिया</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($polices as $index => $police)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $police->police_name }}</td>
                                <td>{{ $police->buckle_number }}</td>
                                <td>
                                    {{ $police->punishment_given_date ? \Carbon\Carbon::parse($police->punishment_given_date)->format('d-m-Y') : '--' }}
                                </td>
                                <td>{{ $police->punishment_type ?? '--' }}</td>
                                <td>{{ $police->reason ?? '--' }}</td>

                                <td>
                                    @if ($police->punishment_documents)
                                        <a href="{{ route('punishments.view', $police->punishment_documents) }}"
                                            target="_blank" class="btn btn-sm btn-danger">
                                            <i class="fas fa-file-pdf"></i> पहा
                                        </a>
                                    @else
                                        <span class="text-muted">नाही</span>
                                    @endif
                                </td>

                                <td>
                                    @if (in_array($designation, ['Head_Person']))
                                        <button class="btn btn-sm btn-warning"
                                            onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')">
                                            <i class="fas fa-edit"></i> शिक्षा जोडा
                                        </button>
                                    @endif
                                    <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">कोणतीही नोंद सापडली नाही</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bootstrap 5 Modal -->
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

    <!-- AJAX Modal Script -->
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

        // ✅ Auto-hide alerts
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 4000);
        });
    </script>
@endsection
