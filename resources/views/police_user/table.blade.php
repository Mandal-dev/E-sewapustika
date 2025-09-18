<div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        <thead class="table-light">
            <tr>
                <th>क्रमांक</th>
                <th>नाव</th>
                <th>बकल क्रमांक</th>
                <th>पद</th>
                <th>मोबाइल नंबर</th>
                <th>विभाग</th>
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
                    <td>{{ $police->mobile ?? 'N/A' }}</td>
                    <td>{{ $police->station_name ?? 'N/A' }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <!-- Edit Icon -->
                            <button class="btn btn-primary btn-sm"
                                onclick="openModal('{{ route('police.edit', $police->id) }}')" title="Edit"
                                style="padding: 6px 10px; border-radius: 50%;">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- View Icon -->
                            <a href="{{ route('police_profile.index', $police->id) }}" class="btn btn-info btn-sm"
                                title="View" style="padding: 6px 10px; border-radius: 50%;">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
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
