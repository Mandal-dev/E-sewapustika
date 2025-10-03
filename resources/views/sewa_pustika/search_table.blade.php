        @php
            $designation = Session::get('user.designation_type');
        @endphp

        <div class="dashboard-content">
            <!-- Table for Desktop -->
            <div class="table-responsive d-none d-md-block" style="max-height:400px; overflow-y:auto; padding:10px;">
                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">
                        <tr>
                            <th>Sr. No</th>
                            <th>Department</th>
                            <th>Post</th>
                            <th>Police Name</th>
                            <th>Mobile No</th>
                            <th>Buckle No.</th>
                            <th>Sewa Pustika</th>
                            <th>स्थिती</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polices as $index => $police)
                            <tr>
                                <td>{{ $polices->firstItem() + $index }}</td>
                                <td>{{ $police->police_station_name }}</td>
                                <td>{{ $police->post }}</td>
                                <td>{{ $police->police_name }}</td>
                                <td>{{ $police->mobile }}</td>
                                <td>{{ $police->buckle_number }}</td>
                                <td>
                                    @if ($police->sewapusticapath)
                                        <a href="{{ route('sewapustika.view', $police->sewapusticapath) }}"
                                            target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-file-pdf"></i> पहा
                                        </a>
                                    @else
                                        <span class="text-muted">नाही</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $status = strtolower($police->review_status); // Use custom_status
                                    @endphp

                                    @if ($status === 'approved')
                                        <span class="badge bg-success text-white status-badge" style="cursor:pointer"
                                            data-variable="{{ $police->remark ?? 'No remark provided' }}"
                                            data-label="टीप:" data-title="मंजूर">मंजूर</span>
                                    @elseif ($status === 'rejected')
                                        <span class="badge bg-danger text-white status-badge" style="cursor:pointer"
                                            data-variable="{{ $police->remark ?? 'No remark provided' }}"
                                            data-label="नाकारण्याचे कारण:" data-title="नाकारले">नाकारले</span>
                                    @elseif ($status === 'uploaded')
                                        <span class="badge bg-info text-white status-badge" style="cursor:pointer"
                                            data-variable="शिक्षा अपलोड झाली आहे, पण पुनरावलोकन अद्याप झाले नाही"
                                            data-label="स्थिती:" data-title="अपलोड">अपलोड</span>
                                    @else
                                        {{-- pending --}}
                                        <span class="badge bg-warning text-dark status-badge" style="cursor:pointer"
                                            data-variable="शिक्षा अजून प्रलंबित आहे" data-label="स्थिती:"
                                            data-title="प्रलंबित">प्रलंबित</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Add Button -->
                                        <button class="btn btn-primary btn-sm menuBtn"
                                            data-url="{{ route('sewa_pustika.addshow', $police->police_user_id) }}"
                                            title="Add" style="padding: 6px 10px; border-radius: 50%;">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                        <!-- View Button -->
                                        <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                            class="btn btn-info btn-sm" title="View"
                                            style="padding: 6px 10px; border-radius: 50%;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($designation === 'Head_Person' && $police->sewapustika_id && strtolower($police->review_status) === 'pending')
                                            <button class="btn btn-sm btn-warning menuBtn"
                                                style="padding: 6px 10px; border-radius: 50%;"
                                                data-url="{{ route('sewapustika.approval.show', $police->sewapustika_id) }}">
                                                <i class="fas fa-check me-1"></i>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">कोणतीही नोंद सापडली नाही</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($polices as $police)
                    <div class="officer-card mb-3 p-3 border rounded">
                        <div class="left-col mb-2">
                            <p><strong>Department:</strong> {{ $police->police_station_name }}</p>
                            <p><strong>Post:</strong> {{ $police->post }}</p>
                            <p><strong>Police Name:</strong> {{ $police->police_name }}</p>
                            <p><strong>Mobile No:</strong> {{ $police->mobile }}</p>
                            <p><strong>Buckle No:</strong> {{ $police->buckle_number }}</p>
                        </div>
                        <div class="right-col d-flex flex-wrap gap-2">
                            <!-- Sewa Pustika -->
                            @if ($police->sewapusticapath)
                                <a href="{{ route('sewapustika.view', $police->sewapusticapath) }}"
                                    class="btn btn-sm btn-primary flex-grow-1">
                                    <i class="fas fa-file-pdf"></i> View
                                </a>
                            @else
                                <span class="text-muted">नाही</span>
                            @endif

                            <!-- Add -->
                            <button class="btn btn-sm btn-primary menuBtn flex-grow-1"
                                data-url="{{ route('sewa_pustika.addshow', $police->police_user_id) }}">
                                <i class="fas fa-plus"></i> Add
                            </button>

                            <!-- View Profile -->
                            <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                class="btn btn-sm btn-info flex-grow-1">
                                <i class="fas fa-eye"></i> Profile
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">कोणतीही नोंद सापडली नाही</div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    Showing {{ $polices->firstItem() }} to {{ $polices->lastItem() }}
                    of {{ $polices->total() }} records
                    (Page {{ $polices->currentPage() }} of {{ $polices->lastPage() }})
                </div>
                <div>
                    {!! $polices->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
