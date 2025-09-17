    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                    <table class="table table-bordered align-middle my-rounded-table">
                        <thead class="table-light">
                <tr>
                    <th>क्रमांक</th>
                    <th>अधिकाऱ्याचे नाव</th>
                    <th>बकल क्रमांक</th>
                    <th>शिक्षेची तारीख</th>
                    <th>शिक्षेचे प्रकार</th>
                    <th>शिक्षेचे कारण</th>
                    <th>दस्तऐवज</th>

                </tr>
            </thead>
            <tbody>
                @forelse($punishments as $index => $police)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $police->police_name }}</td>
                        <td>{{ $police->buckle_number }}</td> <!-- corrected -->
                        <td>{{ \Carbon\Carbon::parse($police->punishment_given_date)->format('d-m-Y') }}</td>
                        <td>{{ $police->punishment_type ?? '--' }}</td>
                        <td>{{ $police->reason ?? '--' }}</td>
                        <td>
                            @if ($police->punishment_documents)
                                <a href="{{ route('punishments.view', $police->punishment_documents) }}" target="_blank"
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
                        <td colspan="8" class="text-center">कोणतीही नोंद सापडली नाही</td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>
