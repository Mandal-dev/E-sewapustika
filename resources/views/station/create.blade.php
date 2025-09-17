<style>
    .modal-body {
    background: url("{{ asset('img/registration form bg.png') }}") no-repeat center center;
    background-size: cover;
}
</style>


<form action="{{ route('stations.store') }}" method="POST">
    @csrf

    <div class="modal-body" style="padding: 1.5rem; background-color: #fff;">

        <div class="row">
            <!-- Country -->
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">देशाचे नाव <span class="text-danger">*</span></label>
                <select name="country_id" id="countrySelect" class="form-select" required>
                    <option value="">-- देश निवडा --</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- State -->
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">राज्याचे नाव <span class="text-danger">*</span></label>
                <select name="state_id" id="stateSelect" class="form-select" required>
                    <option value="">-- राज्य निवडा --</option>
                </select>
            </div>
        </div>

        <div class="row">
            <!-- District -->
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">जिल्हा <span class="text-danger">*</span></label>
                <select name="district_id" id="districtSelect" class="form-select" required>
                    <option value="">-- जिल्हा निवडा --</option>
                </select>
            </div>

            <!-- City -->
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">शहर <span class="text-danger">*</span></label>
                <select name="city_id" id="citySelect" class="form-select" required>
                    <option value="">-- शहर निवडा --</option>
                </select>
            </div>
        </div>

        <!-- Police Station Name -->
        <div class="mb-3 text-start">
            <label class="form-label fw-semibold">ठाणे नाव <span class="text-danger">*</span></label>
            <input type="text" name="city_name" class="form-control" placeholder="ठाणे नाव" required>

        </div>

        <!-- Status -->
        <div class="mb-3 text-start">
            <label class="form-label fw-semibold">स्थिती:</label>
            <select name="status" class="form-select">
                <option value="Active" selected>सक्रिय</option>
                <option value="Inactive">निष्क्रिय</option>
            </select>
        </div>
    </div>

    <div class="modal-footer d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-success px-4">सबमिट करा</button>
        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">रद्द करा</button>
    </div>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function () {
    // Country → State
    $('#countrySelect').on('change', function () {
        let countryId = $(this).val();
        $('#stateSelect').html('<option value="">लोड करत आहे...</option>');
        $.get(`/states/by-country/${countryId}`, function (data) {
            let options = '<option value="">-- राज्य निवडा --</option>';
            data.forEach(item => options += `<option value="${item.id}">${item.state_name}</option>`);
            $('#stateSelect').html(options);
            $('#districtSelect, #citySelect').html('<option value="">-- निवडा --</option>');
        });
    });

    // State → District
    $('#stateSelect').on('change', function () {
        let stateId = $(this).val();
        $('#districtSelect').html('<option value="">लोड करत आहे...</option>');
        $.get(`/districts/by-state/${stateId}`, function (data) {
            let options = '<option value="">-- जिल्हा निवडा --</option>';
            data.forEach(item => options += `<option value="${item.id}">${item.district_name}</option>`);
            $('#districtSelect').html(options);
            $('#citySelect').html('<option value="">-- निवडा --</option>');
        });
    });

    // District → City
    $('#districtSelect').on('change', function () {
        let districtId = $(this).val();
        $('#citySelect').html('<option value="">लोड करत आहे...</option>');
        $.get(`/cities/by-district/${districtId}`, function (data) {
            let options = '<option value="">-- शहर निवडा --</option>';
            data.forEach(item => options += `<option value="${item.id}">${item.city_name}</option>`);
            $('#citySelect').html(options);
        });
    });
});
</script>
