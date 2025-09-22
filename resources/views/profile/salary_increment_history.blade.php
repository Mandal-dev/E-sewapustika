<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@if (isset($error) && $error)
    <div class="alert alert-danger mt-2">
        {{ $error }}
    </div>
@endif

@php
    $designation = Session::get('user.designation_type');
@endphp

<!-- ================= Desktop Table ================= -->
<div class="table-responsive d-none d-md-block" style="max-height:400px;overflow-y:auto;padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        <thead class="table-light">
            <tr>
                <th>क्रमांक</th>
                <th>Department</th>
                <th>नाव</th>
                <th>बकल क्रमांक</th>
                <th>वेतनवाढ दिनांक</th>
                <th>Present Days</th>
                <th>वेतनवाढ प्रकार</th>
                <th>Level</th>
                <th>ग्रेड पेमेण्ट</th>
                <th>नवीन वेतन</th>
                <th>वाढलेली रक्कम</th>
                <th>कागदपत्र</th>
                @if ($designation === 'Head_Person')
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($increments as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->station_name ?? '-' }}</td>
                    <td>{{ $item->police_name ?? '--' }}</td>
                    <td>{{ $item->buckle_number ?? '--' }}</td>
                    <td>{{ $item->increment_date ? \Carbon\Carbon::parse($item->increment_date)->format('d-m-Y') : '--' }}</td>
                    <td>{{ $item->present_days ?? '--' }}</td>
                    <td>{{ $item->increment_type ?? '--' }}</td>
                    <td>{{ $item->level ?? '--' }}</td>
                    <td>{{ $item->grade_pay ?? '--' }}</td>
                    <td>{{ $item->new_salary ?? '--' }}</td>
                    <td>{{ $item->increased_amount ?? '--' }}</td>
                    <td class="text-center">
                        @if ($item->increment_documents)
                            <a href="{{ route('salary_increment.view', $item->increment_documents) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> पहा
                            </a>
                        @else
                            <span class="text-muted">नाही</span>
                        @endif
                    </td>
                    @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                        <td class="text-center">
                            <button class="btn btn-primary btn-sm" onclick="openModal('{{ route('salary_increment.add', $item->police_user_id) }}')" title="Add Increment" style="padding: 6px 10px; border-radius: 50%;">
                                <i class="fas fa-plus"></i>
                            </button>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center text-muted">कोणतीही नोंद सापडली नाही</td>

                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ================= Mobile Card View ================= -->
<div class="d-md-none">
    @forelse($increments as $index => $item)
        <div class="card mb-3 shadow-sm rounded-3 border-0">
            <!-- Card Header -->
            <div class="card-header d-flex justify-content-between align-items-center" style="background: rgb(233, 245, 255); border-radius: 0.75rem 0.75rem 0 0;">
                <span><strong>#{{ $index + 1 }} - {{ $item->police_name ?? '--' }}</strong></span>
                 @if ($designation === 'Head_Person' || $designation === 'Account_Department')
                    <button class="btn btn-primary btn-sm" onclick="openModal('{{ route('salary_increment.add', $item->police_user_id) }}')" title="Add Increment" style="padding: 4px 8px; border-radius: 50%;">
                        <i class="fas fa-plus"></i>
                    </button>
                @endif
            </div>

            <!-- Card Body -->
            <div class="card-body p-3">
                <div class="row mb-2">
                    <div class="col-6"><strong>Department:</strong> {{ $item->station_name ?? '--' }}</div>
                    <div class="col-6"><strong>बकल क्रमांक:</strong> {{ $item->buckle_number ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>वेतनवाढ दिनांक:</strong> {{ $item->increment_date ? \Carbon\Carbon::parse($item->increment_date)->format('d-m-Y') : '--' }}</div>
                    <div class="col-6"><strong>Present Days:</strong> {{ $item->present_days ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>वेतनवाढ प्रकार:</strong> {{ $item->increment_type ?? '--' }}</div>
                    <div class="col-6"><strong>Level:</strong> {{ $item->level ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>ग्रेड पेमेण्ट:</strong> {{ $item->grade_pay ?? '--' }}</div>
                    <div class="col-6"><strong>नवीन वेतन:</strong> {{ $item->new_salary ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>वाढलेली रक्कम:</strong> {{ $item->increased_amount ?? '--' }}</div>
                    <div class="col-6"><strong>कारण:</strong> {{ $item->reason ?? '--' }}</div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>कागदपत्र:</strong>
                        @if ($item->increment_documents)
                            <a href="{{ route('salary_increment.view', $item->increment_documents) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> पहा
                            </a>
                        @else
                            <span class="text-muted">नाही</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-muted">कोणतीही नोंद सापडली नाही</p>
    @endforelse
</div>
