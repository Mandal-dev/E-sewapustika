@extends('Dashboard.header')

@section('data')
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/new_profile.css') }}">

    <!-- Officer Profile Card -->
    <div class="officer-card">
        <!-- Edit Button -->
        <a onclick="openModal('{{  route('police.edit', $police->police_user_id ?? 0) }}')" class="edit-btn" title="Edit Profile">
            <span class="material-icons">edit</span>
        </a>


        <img src="{{ asset('img/default_img.png') }}" alt="Officer">
        <div class="officer-info">
            <h2>{{ $police->police_name ?? 'Not available' }}</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="label">{{ __('messages.buckle_no') }}:</span>
                    <span class="value">{{ $police->buckle_number ?? 'N/A' }}</span>
                </div>
                <div class="info-item status">
                    <span class="status-dot"></span>
                    {{ $police->city_status ?? __('messages.status') }}
                </div>
                <div class="info-item">
                    <span class="material-icons">email</span>
                    {{ __('messages.email') }}: {{ $police->email ?? __('messages.not_available') }}
                </div>
                <div class="info-item">
                    <span class="material-icons">phone</span>
                    {{ __('messages.contact') }}: {{ $police->contact ?? __('messages.not_available') }}
                </div>
                <div class="info-item">
                    <span class="label">{{ __('messages.station') }}:</span>
                    <span class="value">{{ $police->police_station_name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('messages.district') }}:</span>
                    <span class="value">{{ $police->district_name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="material-icons">location_on</span>
                    <span class="value">{{ $police->state_name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="material-icons">location_city</span>
                    <span class="value">{{ $police->city_name ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs-container mt-4 card">
        <div class="tab-nav tabs">
            <button class="tab-button active" data-tab="sewa_pustika">{{ __('messages.sewa_pustika') }}</button>
            <button class="tab-button" data-tab="vetanwadh">{{ __('messages.vetanwadh') }}</button>
            <button class="tab-button" data-tab="bakshish">{{ __('messages.bakshish') }}</button>
            <button class="tab-button" data-tab="shiksha">{{ __('messages.shiksha') }}</button>
            <button class="tab-button" data-tab="raja">{{ __('messages.raja') }}</button>
            <button class="tab-button" data-tab="help">{{ __('messages.help') }}</button>
        </div>

        <!-- Table -->
        <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
            <table id="police_data_table" class="table table-bordered align-middle my-rounded-table">
                <thead class="table-light">
                    <tr id="table_head">
                        <th>Loading...</th>
                    </tr>
                </thead>
                <tbody id="table_body">
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="spinner-border text-primary" role="status"></div>
                        </td>
                    </tr>
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
                        <span class="visually-hidden">लोड होत आहे...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/table-scroll.js') }}"></script>

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

            // Optional: Load content
            $.get(url, function(response) {
                $('#sewaPustikaModalBody').html(response);
            }).fail(function() {
                $('#sewaPustikaModalBody').html(
                    '<div class="p-4 text-center text-danger">डेटा लोड करण्यात अडचण आली.</div>'
                );
            });
        }
                    $(document).on('click', '.menuBtn', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                openModal(url);
            });

        const tabButtons = document.querySelectorAll('.tab-button');
        const policeId = {{ $police->police_user_id ?? 0 }};

        function loadingTable() {
            return `<tr><td colspan="10" class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
            </td></tr>`;
        }


        function loadTabData(tab) {
            $('#table_body').html(loadingTable());
            $('#table_head').html('<th>Loading...</th>');

            let url = '';
            switch (tab) {
                case 'sewa_pustika': url = `/police/sewa-pustika/${policeId}`; break;
                case 'shiksha': url = `/punishments/history/${policeId}`; break;
                case 'bakshish': url = `/rewards/history/${policeId}`; break;
                case 'vetanwadh': url = `/salary_increment/history/${policeId}`; break;
                case 'raja': url = `/leave/history/${policeId}`; break;
                case 'help': url = `/issues/history/${policeId}`; break;
            }

            $.get(url, function(response) {
                $('#police_data_table').html(response);
            }).fail(function() {
                $('#table_body').html(
                    `<tr><td colspan="10" class="text-center text-danger">डेटा लोड करण्यात अडचण आली.</td></tr>`
                );
            });
        }

        // Load default tab
        loadTabData('sewa_pustika');

        // Tab click
        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                tabButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                loadTabData(btn.dataset.tab);
            });
        });
    });
    </script>
@endsection
