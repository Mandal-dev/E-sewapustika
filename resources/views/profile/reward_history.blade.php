    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                    <table class="table table-bordered align-middle my-rounded-table">
                        <thead class="table-light">
            <tr>
                <th>क्रमांक</th>
                <th>अधिकाऱ्याचे नाव</th>
                <th>बकल क्रमांक</th>
                <th>बक्षीस दिल्याची तारीख</th>
                <th>बक्षीस प्रकार</th>
                <th>कारण</th>
                <th>दस्तऐवज</th>
            </tr>
        </thead>
        <tbody>
            @forelse($punishments as $index => $reward)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $reward->police_name }}</td>
                    <td>{{ $reward->buckle_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($reward->reward_given_date)->format('d-m-Y') }}</td>
                    <td>{{ $reward->reward_type ?? '--' }}</td>
                    <td>{{ $reward->reason ?? '--' }}</td>
                    <td>
                        @if ($reward->rewards_documents)
                            <a href="{{ route('rewards.view', $reward->rewards_documents) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> पहा
                            </a>
                        @else
                            <span class="text-muted">नाही</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">कोणतीही नोंद सापडली नाही</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
