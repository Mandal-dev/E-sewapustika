<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

@if (isset($error) && $error)
    <div class="alert alert-danger mt-2">
        {{ $error }}
    </div>
@endif

<div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
                    <table class="table table-bordered align-middle my-rounded-table">
                        <thead class="table-light">
            <tr>
                <th>क्रमांक</th>
                <th>पोलीस ठाण्याचे नाव</th>
                <th>अधिकाऱ्याचे नाव</th>
                <th>बकल क्रमांक</th>
                <th>वेतन वाढ दिनांक</th>
                <th>वाढीचा प्रकार</th>
                <th>नवीन वेतन</th>
                <th>भत्ता</th>
                <th>नेट वेतन</th>
                <th>ग्रेड पेमेण्ट</th>
                <th>वेतन वाढीची रक्कम</th>
                <th>कारण</th>
                <th>कागदपत्र</th>
            </tr>
        </thead>
        <tbody>
            @forelse($increments as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->station_name ?? '-' }}</td>
                    <td>{{ $item->police_name ?? '--' }}</td>
                    <td>{{ $item->buckle_number ?? '--' }}</td>
                    <td>
                        @if ($item->increment_date)
                            {{ \Carbon\Carbon::parse($item->increment_date)->format('d-m-Y') }}
                        @else
                            --
                        @endif
                    </td>
                    <td>{{ $item->increment_type ?? '--' }}</td>
                    <td>{{ $item->new_salary ?? '--' }}</td>
                    <td>{{ $item->allowance ?? '--' }}</td>
                    <td>{{ $item->net_salary ?? '--' }}</td>
                    <td>{{ $item->grade_pay ?? '--' }}</td>
                    <td>{{ $item->increased_amount ?? '--' }}</td>
                    <td>{{ $item->reason ?? '--' }}</td>
                    <td class="text-center">
                        @if ($item->increment_documents)
                            <a href="{{ asset('storage/increments/' . $item->increment_documents) }}" target="_blank"
                                class="btn btn-sm btn-danger" title="कागदपत्र पहा">
                                <i class="fas fa-file-pdf"></i> पहा
                            </a>
                        @else
                            <span class="text-muted">नाही</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center text-muted">कोणतीही नोंद सापडली नाही</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
