<br>
<div class="table-section p-3" style="background: #fff; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);">
    <h5 class="mb-2 fw-semibold">पोलीसांची यादी</h5>
    <p class="text-muted mb-3">एकूण नोंदी: @php($policeUsers = $policeUsers ?? collect())</p>

    <div class="table-responsive ps-2" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light sticky-top" style="background: #FFCC06;">
                <tr>
                    <th>क्रमांक</th>
                    <th>नाव</th>
                    <th>बकल क्रमांक</th>
                    <th>पद</th>
                    <th>ठाणे</th>
                    <th>क्रिया</th> <!-- ✅ New column for actions -->
                </tr>
            </thead>

            <tbody>
                @forelse ($policeUsers as $key => $police)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $police->police_name ?? 'N/A' }}</td>
                        <td>{{ $police->buckle_number ?? 'N/A' }}</td>
                        <td>{{ $police->designation_type ?? 'N/A' }}</td>
                        <td>{{ $police->station_name ?? 'N/A' }}</td>
                        <td>
                            <button class="btn btn-primary" onclick="openModal('{{ route('police.edit', $police->id) }}')">
                                 Edit
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">पोलीस आढळले नाहीत</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
