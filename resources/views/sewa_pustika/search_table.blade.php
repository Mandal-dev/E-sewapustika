<div class="table-section p-3" style="background: #fff; border-radius: 8px;">
            <h5 class="mb-2 fw-semibold">Sewa pustika</h5>

            <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">
                        <thead class="table-light">
                            <tr>
                                <th>Sr. No</th>
                                <th>Department</th>
                                <th>Post</th>
                                <th>Police Name</th>
                                <th>Mobile no</th>
                                <th>Buckle No.</th>
                                <th>Sewa Pustika</th>
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


                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Edit Icon -->
                                        <button class="btn btn-primary btn-sm menuBtn"
                                            data-url="{{ route('sewa_pustika.addshow', $police->police_user_id) }}"
                                            title="Add" style="padding: 6px 10px; border-radius: 50%;">
                                            <i class="fas fa-plus"></i>
                                        </button>

                                        <!-- View Icon -->
                                        <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                            class="btn btn-info btn-sm" title="View"
                                            style="padding: 6px 10px; border-radius: 50%;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Mobile Card View -->
                            <div class="officer-card d-md-none">
                                <div class="left-col">
                                    <p class="department">Department:{{ $police->police_station_name }}</p>
                                    <p>Post:{{ $police->post }}</p>
                                    <p>Police Name:{{ $police->police_name }}</p>
                                </div>
                                <div class="right-col text-start">



                                    <!-- View button -->

                                    @if ($police->sewapusticapath)
                                        <a class="action-btn mb-2"
                                            href="{{ route('sewapustika.view', $police->sewapusticapath) }}">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">नाही</span>
                                    @endif

                                    <!-- Edit button -->
                                    <button class="action-btn"
                                        onclick="openModal('{{ route('sewa_pustika.addshow', $police->police_user_id) }}')">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <!-- MObile No (single row) -->
                                    <p>Mobile no:{{ $police->mobile }}</p>


                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">कोणतीही नोंद सापडली नाही</td>
                                </tr>
                        @endforelse
                    </tbody>
                </table>
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
