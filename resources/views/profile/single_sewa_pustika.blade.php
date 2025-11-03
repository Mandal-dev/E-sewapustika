<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@php
    $designation = Session::get('user.designation_type');
@endphp

<!-- ================= Desktop Table ================= -->
<div class="table-responsive d-none d-md-block" style="max-height:400px; overflow-y:auto; padding:10px;">
    <table class="table table-bordered align-middle my-rounded-table">
        <thead class="table-light">
            <tr>
                <th>Sr. No</th>
                <th>Station Name</th>
                <th>Police Name</th>
                <th>Buckle No.</th>
                <th>Post</th>
                <th>City</th>
                <th>District</th>
                <th>Document</th>
                @if($designation === 'Head_Person' || $designation === 'Sewapustika_Department')
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($polices as $index => $police)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $police->police_station_name ?? '--' }}</td>
                    <td>{{ $police->police_name ?? '--' }}</td>
                    <td>{{ $police->buckle_number ?? '--' }}</td>
                    <td>{{ $police->post ?? '--' }}</td>
                    <td>{{ $police->city_name ?? '--' }}</td>
                    <td>{{ $police->district_name ?? '--' }}</td>
                    <td>
                        @if($police->sewapusticapath)
                            <a href="{{ route('sewapustika.view', $police->sewapusticapath) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> View
                            </a>
                        @else
                            <span class="text-muted">None</span>
                        @endif
                    </td>
                    @if($designation === 'Head_Person' || $designation === 'Sewapustika_Department')
                        <td>
                            <button class="btn btn-primary btn-sm action-btn menuBtn"
                                data-url="{{ route('sewa_pustika.addshow', $police->police_user_id) }}">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </td>
                    @endif
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
    @forelse($polices as $index => $police)
        <div class="card mb-3 shadow-sm border-0 rounded-3">
            <!-- Card Header -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <span style="background: rgb(233, 245, 255); padding: 4px 8px; border-radius: 6px;">
                    <strong>#{{ $index + 1 }}</strong> - {{ $police->police_name ?? '--' }}
                </span>
                <span class="badge bg-primary text-white">{{ $police->buckle_number ?? '--' }}</span>
            </div>

            <!-- Card Body -->
            <div class="card-body p-3">
                <div class="row mb-2">
                    <div class="col-6"><strong>Station:</strong></div>
                    <div class="col-6 text-end">{{ $police->police_station_name ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Post:</strong></div>
                    <div class="col-6 text-end">{{ $police->post ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>City:</strong></div>
                    <div class="col-6 text-end">{{ $police->city_name ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>District:</strong></div>
                    <div class="col-6 text-end">{{ $police->district_name ?? '--' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Document:</strong></div>
                    <div class="col-6 text-end">
                        @if($police->sewapusticapath)
                            <a href="{{ route('sewapustika.view', $police->sewapusticapath) }}" target="_blank" class="btn btn-sm btn-danger py-0 px-2">
                                <i class="fas fa-file-pdf"></i> View
                            </a>
                        @else
                            <span class="text-muted">None</span>
                        @endif
                    </div>
                </div>
                @if($designation === 'Head_Person' || $designation === 'Sewapustika_Department')
                    <div class="row">
                        <div class="col-6"><strong>Action:</strong></div>
                        <div class="col-6 text-end">
                            <button class="btn btn-primary btn-sm action-btn menuBtn"
                                data-url="{{ route('sewa_pustika.addshow', $police->police_user_id) }}">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-center text-muted">कोणतीही नोंद सापडली नाही</p>
    @endforelse
</div>
