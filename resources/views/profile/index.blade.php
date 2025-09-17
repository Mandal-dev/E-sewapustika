@extends('Dashboard.header')

@section('data')
    <link rel="stylesheet" href="{{ asset('css/new_profile.css') }}">

    <!-- Officer Profile Card -->
    <div class="officer-card">
        <!-- Edit Button -->
        <a href="{{ route('police.edit', $police->police_user_id ?? 0) }}" class="edit-btn" title="Edit Profile">
            <span class="material-icons">edit</span>
        </a>

        <img src="{{ asset('img/default_img.png') }}" alt="Officer">
        <div class="officer-info">
            <h2>{{ $police->police_name ?? 'Not available' }}</h2>
            <div class="info-grid">
                <div class="info-item"><span class="label">Buckle no:</span> <span
                        class="value">{{ $police->buckle_number ?? 'N/A' }}</span></div>
                <div class="info-item status"><span class="status-dot"></span> {{ $police->city_status ?? 'Active' }}</div>
                <div class="info-item"><span class="material-icons">email</span> Email:
                    {{ $police->email ?? 'Not available' }}</div>
                <div class="info-item"><span class="material-icons">phone</span> Contact:
                    {{ $police->contact ?? 'Not available' }}</div>
                <div class="info-item"><span class="label">Station:</span> <span
                        class="value">{{ $police->police_station_name ?? 'N/A' }}</span></div>
                <div class="info-item"><span class="label">District:</span> <span
                        class="value">{{ $police->district_name ?? 'N/A' }}</span></div>
                <div class="info-item"><span class="material-icons">location_on</span> <span
                        class="value">{{ $police->state_name ?? 'N/A' }}</span></div>
                <div class="info-item"><span class="material-icons">location_city</span> <span
                        class="value">{{ $police->city_name ?? 'N/A' }}</span></div>
            </div>
        </div>
    </div>


    <!-- Tabs -->
    <div class="tabs-container mt-4 card">
        <div class="tab-nav tabs">
            <button class="tab-button active" data-tab="sewa_pustika">सेवा पुस्तिका</button>
            <button class="tab-button" data-tab="vetanwadh">वेतनवाढ</button>
            <button class="tab-button" data-tab="bakshish">बक्षीस</button>
            <button class="tab-button" data-tab="shiksha">शिक्षा</button>
            <button class="tab-button" data-tab="raja">रजा</button>
            <button class="tab-button" data-tab="help">समस्या व समाधान</button>
        </div>

        <!-- Single Table -->
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    case 'sewa_pustika':
                        url = `/police/sewa-pustika/${policeId}`;
                        break;
                    case 'shiksha':
                        url = `/punishments/history/${policeId}`;
                        break;
                    case 'bakshish':
                        url = `/rewards/history/${policeId}`;
                        break;
                    case 'vetanwadh':
                        url = `/salary_increment/history/${policeId}`;
                        break;
                    case 'raja':
                        url = `/leave/history/${policeId}`;
                        break;
                    case 'help':
                        url = `/issues/history/${policeId}`;
                        break;
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


    <script>
        function openEditModal(policeId) {
            // Show Bootstrap modal
            $('#editModal').modal('show');

            // Load Blade via AJAX
            $.ajax({
                url: `/police/edit/${policeId}`, // Controller should return Blade content
                type: 'GET',
                success: function(response) {
                    $('#editModalContent').html(response);
                },
                error: function() {
                    $('#editModalContent').html(
                        '<div class="p-4 text-center text-danger">Failed to load form. Please try again.</div>'
                        );
                }
            });
        }
    </script>
    <script src="{{ asset('js/table-scroll.js') }}"></script>

@endsection
