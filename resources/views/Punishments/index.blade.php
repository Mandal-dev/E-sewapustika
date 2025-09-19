@extends('Dashboard.header')

@section('data')
    <!-- Bootstrap + Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">

    <!-- jQuery + Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <div class="app-content" style="margin: 0; padding: 1rem;">
        @php
            $designation = Session::get('user.designation_type');
        @endphp

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
            <h5 class="mb-2 fw-semibold">शिक्षा यादी</h5>
            <div class="table-responsive" style="max-height:400px; overflow-y:auto; padding:10px;">
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
                    <tbody id="tableBody">
                        @forelse($polices as $index => $police)
                        <tr>
                            <td>{{ $polices->firstItem() + $index }}</td>
                            <td>{{ $police->police_name }}</td>
                            <td>{{ $police->buckle_number }}</td>
                            <td>{{ $police->punishment_given_date ?? '--' }}</td>
                            <td>{{ $police->punishment_type ?? '--' }}</td>
                            <td>{{ $police->reason ?? '--' }}</td>
                            <td>
                                @if($police->punishment_documents)
                                    <a href="{{ route('punishments.view', ['file' => $police->punishment_documents]) }}" target="_blank" class="btn btn-sm btn-danger">
                                        <i class="fas fa-file-pdf"></i> पहा
                                    </a>
                                @else
                                    <span class="text-muted">नाही</span>
                                @endif
                            </td>
                            <td>
                                @if(Session::get('user.designation_type') === 'Head_Person')
                                    <button class="btn btn-sm btn-warning" onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')">
                                        <i class="fas fa-edit"></i> शिक्षा जोडा
                                    </button>
                                @endif
                                <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">कोणतीही नोंद सापडली नाही</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $polices->firstItem() }} to {{ $polices->lastItem() }} of {{ $polices->total() }} records
                    (Page {{ $polices->currentPage() }} of {{ $polices->lastPage() }})
                </div>
                <div>
                    {!! $polices->links('pagination::bootstrap-5') !!}
                </div>
            </div>
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
    </div>

    <!-- Scripts -->
    <script>
        // Modal open function
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
                    $('#sewaPustikaModalBody').html(`
                        <div class="p-5 text-danger text-center">
                            डेटा लोड करण्यात अडचण आली.
                        </div>
                    `);
                }
            });
        }

        $(document).ready(function() {
            // trigger search
            $('#searchInput').on('keyup', function() {
                performSearch();
            });
            $('#designationFilter').on('change', function() {
                performSearch();
            });
            $('#searchBtn').on('click', function() {
                performSearch();
            });

            function performSearch(page = 1) {
                let keyword = $('#searchInput').val();
                let designation = $('#designationFilter').val();

                $.ajax({
                    url: "{{ route('punishments.search') }}",
                    type: 'GET',
                    data: { keyword, designation, page },
                    success: function(response) {
                        if (response.status === 'success') {
                            updateTable(response.data);
                        } else {
                            $('#tableBody').html(`
                                <tr>
                                    <td colspan="8" class="text-center text-danger">डेटा सापडला नाही</td>
                                </tr>
                            `);
                        }
                    },
                    error: function() {
                        $('#tableBody').html(`
                            <tr>
                                <td colspan="8" class="text-center text-danger">सर्व्हरमध्ये त्रुटी आली</td>
                            </tr>
                        `);
                    }
                });
            }

            function updateTable(data) {
                let rows = '';
                if (data.data.length > 0) {
                    $.each(data.data, function(index, police) {
                        rows += `
                            <tr>
                                <td>${data.from + index}</td>
                                <td>${police.police_name}</td>
                                <td>${police.buckle_number}</td>
                                <td>${police.punishment_given_date ?? '--'}</td>
                                <td>${police.punishment_type ?? '--'}</td>
                                <td>${police.reason ?? '--'}</td>
                                <td>
                                    ${police.punishment_documents
                                        ? `<a href="/punishments/view/${police.punishment_documents}" target="_blank" class="btn btn-sm btn-danger">
                                            <i class="fas fa-file-pdf"></i> पहा
                                           </a>`
                                        : `<span class="text-muted">नाही</span>`}
                                </td>
                                <td>
                                    @if(Session::get('user.designation_type') === 'Head_Person')
                                        <button class="btn btn-sm btn-warning" onclick="openModal('/punishment/add/${police.police_user_id}')">
                                            <i class="fas fa-edit"></i> शिक्षा जोडा
                                        </button>
                                    @endif
                                    <a href="/police_profile/${police.police_user_id}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    rows = `
                        <tr>
                            <td colspan="8" class="text-center">कोणतीही नोंद सापडली नाही</td>
                        </tr>
                    `;
                }
                $('#tableBody').html(rows);
            }
        });
    </script>
@endsection
