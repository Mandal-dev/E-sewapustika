<div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        <thead class="table-light">
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
                    <td>{{ $police->post ?? 'N/A' }}</td>
                    <td>{{ $police->station_name ?? 'N/A' }}</td>
                    <td>
                        <button class="cus-btn btn btn-primary"
                            onclick="openModal('{{ route('police.edit', $police->id) }}')">
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
