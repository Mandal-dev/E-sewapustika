<div class="table-section p-3 mb-3" style="background: #fff; border-radius: 8px;">
    <h5 class="mb-2 fw-semibold">पोलीस ठाण्यांची यादी</h5>
    <p class="text-muted mb-3">एकूण नोंदी: {{ $stations->count() }}</p>

    <div class="table-responsive ps-2" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light sticky-top">
                <tr>
                    <th>क्रमांक</th>
                    <th>राज्य</th>
                    <th>जिल्हा</th>
                    <th>शहर</th>
                    <th>ठाणे</th>
                    <th>स्थिती</th>
                    <th>क्रिया</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stations as $key => $station)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $station->state_name ?? 'N/A' }}</td>
                        <td>{{ $station->district_name ?? 'N/A' }}</td>
                        <td>{{ $station->city_name ?? 'N/A' }}</td>
                        <td>{{ $station->station_name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $station->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                {{ $station->status }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-warning d-flex align-items-center gap-1">
                                <i class="fas fa-edit"></i> संपादन
                            </button>
                        </td>
                    </tr>

                    <!-- Mobile Card Row -->
                    <div class="officer-card d-md-none">
                        <div class="left-col">
                            <p class="state"><strong>State:</strong>{{ $station->state_name ?? 'N/A' }}</p>
                            <p><strong>District:</strong>{{ $station->district_name ?? 'N/A' }}</p>
                            <p><strong>City Name:</strong>{{ $station->city_name ?? 'N/A' }}</p>

                        </div>


                        <div class="right-col text-start">

                            <!-- Edit button -->
                            <button class="action-btn"
                                onclick="openModal('{{ route('sewa_pustika.addshow', $police->police_user_id) }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <!-- Status (single row) -->
                            <p class=" mb-2 d-flex align-items-center">
                                Status:&nbsp;
                                <span class="status text-success d-flex align-items-center">
                                    <i class="fas fa-circle me-1" style="font-size:8px;"></i> Active
                                </span>
                            </p>

                            <p><strong>Station Name:</strong>{{ $station->station_name ?? 'N/A' }}</p>


                        </div>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">नोंदी आढळल्या नाहीत</td>
                        </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
