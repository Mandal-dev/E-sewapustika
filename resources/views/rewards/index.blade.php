@extends('Dashboard.header')

@section('data')
<!-- Bootstrap + Custom CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/sewa_pustika.css') }}">

<!-- jQuery (required for AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="app-content" style="margin: 0; padding: 1rem;">

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>यशस्वी:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>चूक:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search Section -->
    <div class="search-section p-3 d-flex flex-wrap align-items-center gap-2 mb-2"
        style="background: #fff; border-radius: 8px;">
        <input type="text" id="searchKeyword" class="form-control" placeholder="नाव, ठाणे किंवा बकल क्रमांक"
            style="min-width: 220px; flex: 1;">

        <select id="searchDesignation" class="form-select" style="width: 180px;">
            <option value="">सर्व बक्षीस जोडा</option>
            <option value="Police">पोलीस</option>
            <option value="Station_Head">स्टेशन हेड</option>
            <option value="Head_Person">हेड पर्सन</option>
            <option value="Admin">ॲडमिन</option>
        </select>

        <button class="btn btn-success" id="searchBtn"><i class="fas fa-search"></i> शोधा</button>
    </div>

    <!-- Table Section (Desktop) -->
    <div class="table-section p-3 d-none d-md-block" style="background: #fff; border-radius: 8px;">
        <h5 class="mb-2 fw-semibold">बक्षीस यादी</h5>

        <div class="table-responsive" style="max-height:400px;overflow-y:auto;padding:10px;">
            <table class="table table-bordered align-middle my-rounded-table">
                <thead class="table-light">
                    <tr>
                        <th>क्रमांक</th>
                        <th>अधिकाऱ्याचे नाव</th>
                        <th>बकल क्रमांक</th>
                        <th>पद</th>
                        <th>बक्षीस दिनांक</th>
                        <th>बक्षिसांचे प्रकार</th>
                        <th>बक्षिसांचे कारण</th>
                        <th>कागदपत्र</th>
                        <th>स्थिती</th>
                        <th>क्रिया</th>
                    </tr>
                </thead>
                <tbody id="rewardTableBody">
                    @include('rewards.table-rows', ['polices' => $polices])
                </tbody>
            </table>
        </div>
    </div>

    <!-- Card Section (Mobile) -->
    <div class="d-md-none" id="rewardCards">
        <!-- Cards will be injected by JS -->
    </div>

    <!-- Pagination -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
        <div class="text-muted small">
            दर्शवित आहे <strong>{{ $polices->firstItem() }}</strong> ते
            <strong>{{ $polices->lastItem() }}</strong> पैकी
            <strong>{{ $polices->total() }}</strong> नोंदी
            <span class="ms-2">(पान {{ $polices->currentPage() }} / {{ $polices->lastPage() }})</span>
        </div>
        <nav>
            {!! $polices->links('pagination::bootstrap-5') !!}
        </nav>
    </div>
</div>

<!-- Scripts -->
<script>
function openModal(url) {
    const modalElement = document.getElementById('sewaPustikaModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    $('#sewaPustikaModalBody').html(`<div class="p-5 text-center"><div class="spinner-border text-primary" role="status"></div></div>`);

    $.get(url, function(response) {
        $('#sewaPustikaModalBody').html(response);
    }).fail(() => {
        $('#sewaPustikaModalBody').html(`<div class="p-5 text-danger text-center">डेटा लोड करण्यात अडचण आली.</div>`);
    });
}

function aproveopenModal(url) {
    const modalElement = document.getElementById('aprovePustikaModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    $('#aproveModalBody').html(`<div class="p-5 text-center"><div class="spinner-border text-primary" role="status"></div></div>`);

    $.get(url, function(response) {
        $('#aproveModalBody').html(response);
    }).fail(() => {
        $('#aproveModalBody').html(`<div class="p-5 text-danger text-center">डेटा लोड करण्यात अडचण आली.</div>`);
    });
}

function viewRejectReason(reason) {
    $('#rejectReasonBody').text(reason);
    new bootstrap.Modal(document.getElementById('rejectReasonModal')).show();
}

// Auto-hide alerts
$(document).ready(function() {
    setTimeout(() => $('.alert').fadeOut('slow'), 4000);

    function fetchRewards() {
        let keyword = $("#searchKeyword").val();
        let designation = $("#searchDesignation").val();

        $.ajax({
            url: "{{ route('rewards.search') }}",
            method: "GET",
            data: { keyword, designation },
            success: function(response) {
                if (response.status === "success") {
                    let tableHtml = "";
                    let cardHtml = "";

                    if (response.data.data.length > 0) {
                        response.data.data.forEach((item, index) => {
                            // Table row
                            tableHtml += `
                            <tr>
                                <td>${response.data.from + index}</td>
                                <td>${item.police_name ?? '--'}</td>
                                <td>${item.buckle_number ?? '--'}</td>
                                <td>${item.role ?? '--'}</td>
                                <td>${item.reward_given_date ?? '--'}</td>
                                <td>${item.reward_type ?? '--'}</td>
                                <td>${item.reason ?? '--'}</td>
                                <td>${item.rewards_documents
                                    ? `<a href="/uploads/rewards/${item.rewards_documents}" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> पहा</a>`
                                    : `<span class="text-muted">नाही</span>`}
                                </td>
                                <td>${item.reward_status.toLowerCase() === 'approved'
                                    ? `<span class="badge bg-success">मंजूर</span>`
                                    : item.reward_status.toLowerCase() === 'rejected'
                                    ? `<span class="badge bg-danger">नाकारले</span>`
                                    : `<span class="badge bg-warning text-dark">प्रलंबित</span>`}
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="openModal('/rewards/add/${item.police_user_id}')">
                                        <i class="fas fa-edit"></i> बक्षीस जोडा
                                    </button>
                                </td>
                            </tr>`;

                            // Mobile card
                            cardHtml += `
                            <div class="card p-3 mb-3 shadow-sm">
                                <p><strong>नाव:</strong> ${item.police_name ?? '--'}</p>
                                <p><strong>बकल नं.:</strong> ${item.buckle_number ?? '--'}</p>
                                <p><strong>पद:</strong> ${item.role ?? '--'}</p>
                                <p><strong>दिनांक:</strong> ${item.reward_given_date ?? '--'}</p>
                                <p><strong>प्रकार:</strong> ${item.reward_type ?? '--'}</p>
                                <p><strong>कारण:</strong> ${item.reason ?? '--'}</p>
                                <p><strong>स्थिती:</strong>
                                    ${item.reward_status.toLowerCase() === 'approved'
                                        ? `<span class="badge bg-success">मंजूर</span>`
                                        : item.reward_status.toLowerCase() === 'rejected'
                                        ? `<span class="badge bg-danger">नाकारले</span>`
                                        : `<span class="badge bg-warning text-dark">प्रलंबित</span>`}
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="btn btn-sm btn-warning" onclick="openModal('/rewards/add/${item.police_user_id}')">
                                        <i class="fas fa-plus"></i> बक्षीस जोडा
                                    </button>
                                    <a href="/police_profile/${item.police_user_id}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    ${item.rewards_documents
                                        ? `<a href="/uploads/rewards/${item.rewards_documents}" target="_blank" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> पहा</a>`
                                        : `<span class="text-muted">नाही</span>`}
                                </div>
                            </div>`;
                        });
                    } else {
                        tableHtml = `<tr><td colspan="10" class="text-center">कोणतीही नोंद सापडली नाही</td></tr>`;
                        cardHtml = `<div class="text-center text-muted">कोणतीही नोंद सापडली नाही</div>`;
                    }

                    $("#rewardTableBody").html(tableHtml);
                    $("#rewardCards").html(cardHtml);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    // search events
    $("#searchKeyword").on("keyup", fetchRewards);
    $("#searchDesignation").on("change", fetchRewards);
    $("#searchBtn").on("click", fetchRewards);
});
</script>
@endsection
