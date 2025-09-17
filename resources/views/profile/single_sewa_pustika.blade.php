<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                    <table class="table table-bordered align-middle my-rounded-table">
                        <thead class="table-light">
                        <tr>
                            <th>Sr. No</th>
                            <th>Station Name</th>
                            <th>Police Name</th>
                            <th>Buckle No.</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polices as $index => $police)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $police->police_station_name }}</td>
                                <td>{{ $police->police_name }}</td>
                                <td>{{ $police->buckle_number }}</td>
                                <td>
                                    @if ($police->sewapusticapath)
                                        <a href="{{ route('sewapustika.view', $police->sewapusticapath) }}" target="_blank"
                                            class="btn btn-sm btn-danger">
                                            <i class="fas fa-file-pdf"></i> पहा
                                        </a>
                                    @else
                                        <span class="text-muted">नाही</span>
                                    @endif
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">कोणतीही नोंद सापडली नाही</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
