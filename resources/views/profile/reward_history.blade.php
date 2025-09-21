<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- ================= Desktop Table ================= -->
<div class="table-responsive d-none d-md-block" style="max-height:400px; overflow-y:auto; padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        <thead class="table-light">
            <tr>
                <th>क्रमांक</th>
                <th>अधिकाऱ्याचे नाव</th>
                <th>बकल क्रमांक</th>
                <th>पद</th>
                <th>बक्षीस दिनांक</th>
                <th>बक्षिसांचे प्रकार</th>
                <th>बक्षिसांचे कारण</th>
                <th>कागदपत्र</th>
                <th>स्थिती</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rewards as $index => $reward)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $reward->police_name ?? '--' }}</td>
                    <td>{{ $reward->buckle_number ?? '--' }}</td>
                    <td>{{ $reward->role ?? '--' }}</td>
                    <td>{{ $reward->reward_given_date ? \Carbon\Carbon::parse($reward->reward_given_date)->format('d-m-Y') : '--' }}
                    </td>
                    <td>{{ $reward->reward_type ?? '--' }}</td>
                    <td>{{ $reward->reason ?? '--' }}</td>
                    <td>
                        @if ($reward->rewards_documents)
                            <a href="{{ route('rewards.view', $reward->rewards_documents) }}" target="_blank"
                                class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> पहा
                            </a>
                        @else
                            <span class="text-muted">नाही</span>
                        @endif
                    </td>
                    <td>
                        @if (strtolower($reward->reward_status) === 'approved')
                            <span class="badge bg-success">मंजूर</span>
                        @elseif(strtolower($reward->reward_status) === 'rejected')
                            <span class="badge bg-danger">नाकारले</span>
                        @else
                            <span class="badge bg-warning text-dark">प्रलंबित</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm"
                            onclick="openModal('{{ route('rewards.add', $reward->police_user_id) }}')">
                            <i class="fas fa-plus"></i> बक्षीस जोडा
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">कोणतीही नोंद सापडली नाही</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ================= Mobile Card View ================= -->
<div class="d-md-none">
    @forelse($rewards as $index => $reward)
        <div class="card mb-3 shadow-sm rounded-3 border-0">
            <!-- Card Header -->
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #e9f5ff; border-radius: 0.75rem 0.75rem 0 0; font-weight: 600;">
                <span>#{{ $index + 1 }} - {{ $reward->police_name ?? '--' }}</span>
                <button class="btn btn-primary btn-sm" onclick="openModal('{{ route('rewards.add', $reward->police_user_id) }}')" title="Add Reward" style="padding: 4px 8px; border-radius: 50%;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>

            <!-- Card Body -->
            <div class="card-body p-3">
                <div class="row mb-2">
                    <div class="col-6"><strong>बकल क्रमांक:</strong> {{ $reward->buckle_number ?? '--' }}</div>
                    <div class="col-6"><strong>पद:</strong> {{ $reward->role ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>बक्षीस दिनांक:</strong> {{ $reward->reward_given_date ? \Carbon\Carbon::parse($reward->reward_given_date)->format('d-m-Y') : '--' }}</div>
                    <div class="col-6"><strong>प्रकार:</strong> {{ $reward->reward_type ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-12"><strong>कारण:</strong> {{ $reward->reason ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-12">
                        <strong>कागदपत्र:</strong>
                        @if ($reward->rewards_documents)
                            <a href="{{ route('rewards.view', $reward->rewards_documents) }}" target="_blank" class="btn btn-sm btn-danger py-0 px-2">
                                <i class="fas fa-file-pdf"></i> पहा
                            </a>
                        @else
                            <span class="text-muted">नाही</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>स्थिती:</strong>
                        @if (strtolower($reward->reward_status) === 'approved')
                            <span class="badge bg-success">मंजूर</span>
                        @elseif(strtolower($reward->reward_status) === 'rejected')
                            <span class="badge bg-danger">नाकारले</span>
                        @else
                            <span class="badge bg-warning text-dark">प्रलंबित</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">कोणतीही नोंद सापडली नाही</p>
    @endforelse
</div>
