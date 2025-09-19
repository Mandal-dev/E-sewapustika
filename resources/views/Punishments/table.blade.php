<div class="table-responsive" style="max-height:400px; overflow-y:auto; padding:10px;">
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
                <th>क्रिया</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            @forelse($polices as $index => $police)
                <tr>
                    <td>{{ ($polices->currentPage() - 1) * $polices->perPage() + $index + 1 }}</td>
                    <td>{{ $police->police_name }}</td>
                    <td>{{ $police->buckle_number }}</td>
                    <td>{{ $police->punishment_given_date ?? '--' }}</td>
                    <td>{{ $police->punishment_type ?? '--' }}</td>
                    <td>{{ $police->reason ?? '--' }}</td>
                    <td>
                        @if($police->punishment_documents)
                            <a href="{{ route('punishments.view', ['file' => $police->punishment_documents]) }}" target="_blank" class="btn btn-sm btn-danger">
                                <i class="fas fa-file-pdf"></i> पहा
                            </a>
                        @else
                            <span class="text-muted">नाही</span>
                        @endif
                    </td>
                    <td>
                        @if(Session::get('user.designation_type') === 'Head_Person')
                            <button class="btn btn-sm btn-warning" onclick="openModal('{{ route('punishment.add', $police->police_user_id) }}')">
                                <i class="fas fa-edit"></i> शिक्षा जोडा
                            </button>
                        @endif
                        <a href="{{ route('police_profile.index', $police->police_user_id) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">कोणताही पोलीस सापडला नाही</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($polices->count() > 0)
<div class="d-flex justify-content-between align-items-center mt-3">
    <div class="text-muted small">
        Showing {{ $polices->firstItem() }} to {{ $polices->lastItem() }} of {{ $polices->total() }} records
        (Page {{ $polices->currentPage() }} of {{ $polices->lastPage() }})
    </div>
    <div>
        {!! $polices->links('pagination::bootstrap-5') !!}
    </div>
</div>
@endif
