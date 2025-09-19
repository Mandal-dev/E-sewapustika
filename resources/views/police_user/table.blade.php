<br>

<div class="table-section p-3" style="background: #fff; border-radius: 8px; box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);">

    <!-- 🔹 Buttons + Search Bar Row -->




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
                        <td data-label="Officer Name">{{ $police->police_name ?? 'N/A' }}</td>
                        <td data-label="Buckle no">{{ $police->buckle_number ?? 'N/A' }}</td>
                        <td data-label="designation">{{ $police->post ?? 'N/A' }}</td>
                        <td data-label="Station">{{ $police->station_name ?? 'N/A' }}</td>
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

                    <!-- Mobile Card View -->
                    <div class="officer-card d-md-none">
                        <div class="left-col">
                            <p class="name">{{ $police->police_name ?? 'N/A' }}</p>
                            <p>Buckle No:{{ $police->buckle_number ?? 'N/A' }}</p>
                            <p>Department: {{ $police->station_name ?? 'N/A' }}</p>
                        </div>
                        <div class="right-col text-start">
                            <!-- View button -->
                            <a class="action-btn mb-2" href="{{ route('police_profile.index', $police->id) }}">
                                <i class="fas fa-eye"></i> View
                            </a>

                            <button class="action-btn" onclick="openModal('{{ route('police.edit', $police->id) }}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>

                            <!-- Status (single row) -->
                            <p class=" mb-2 d-flex align-items-center">
                                Status:&nbsp;
                                <span class="status text-success d-flex align-items-center">
                                    <i class="fas fa-circle me-1" style="font-size:8px;"></i> Active
                                </span>
                            </p>

                            <!-- Designation -->
                            <p class="mb-0">Designation:{{ $police->post ?? 'N/A' }}</p>
                        </div>


                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">पोलीस आढळले नाहीत</td>
                        </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
