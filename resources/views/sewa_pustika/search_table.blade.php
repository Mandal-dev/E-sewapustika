        <div class="table-section p-3" style="background: #fff; border-radius: 8px;">
            <h5 class="mb-2 fw-semibold">Sewa pustika</h5>

            <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                <table class="table table-bordered align-middle my-rounded-table">
                    <thead class="table-light">
                        <thead class="table-light">
                            <tr>
                                <th>Sr. No</th>
                                <th>Station Name</th>
                                <th>Police Name</th>
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
                                <td>{{ $police->police_name }}</td>
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
                                    <button class="btn btn-sm btn-warning"
                                        onclick="openModal('{{ route('sewa_pustika.addshow', $police->police_user_id) }}')">
                                        <i class="fas fa-edit"></i> पुस्तिका जोडा
                                    </button>
                                    <a href="{{ route('police_profile.index', $police->police_user_id) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
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
