<div class="modal-header">
    <h5 class="modal-title fw-bold">नवीन शहर जोडा</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('cities.store') }}" method="POST">
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
        <!-- District -->

        <div class="col-md-6 mb-3 text-start">
            <label class="form-label fw-semibold">जिल्हा <span class="text-danger">*</span></label>
            <select name="district_id" id="districtSelect" class="form-select" required>
                <option value="">-- जिल्हा निवडा --</option>
            </select>
        </div>

        <!-- District Name (English) -->
        <div class="mb-3 text-start">
            <label class="form-label fw-semibold">शहर नाव <span class="text-danger">*</span></label>
            <input type="text" name="city_name" class="form-control" placeholder="शहर नाव" required>
        </div>

        <!-- Marathi and Hindi Names in Same Row -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">शहर नाव (मराठी):</label>
                <input type="text" name="city_name_marathi" class="form-control" placeholder="मराठीत शहर नाव">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">शहर नाव (हिंदी):</label>
                <input type="text" name="city_name_hindi" class="form-control" placeholder="हिंदीत शहर नाव">
            </div>
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
    $(document).ready(function() {
        // Country → State
        $('#countrySelect').on('change', function() {
            let countryId = $(this).val();
            $('#stateSelect').html('<option value="">लोड करत आहे...</option>');
            $.get(`/states/by-country/${countryId}`, function(data) {
                let options = '<option value="">-- राज्य निवडा --</option>';
                data.forEach(item => options +=
                    `<option value="${item.id}">${item.state_name}</option>`);
                $('#stateSelect').html(options);
                $('#districtSelect, #citySelect').html('<option value="">-- निवडा --</option>');
            });
        });

        // State → District
        $('#stateSelect').on('change', function() {
            let stateId = $(this).val();
            $('#districtSelect').html('<option value="">लोड करत आहे...</option>');
            $.get(`/districts/by-state/${stateId}`, function(data) {
                let options = '<option value="">-- जिल्हा निवडा --</option>';
                data.forEach(item => options +=
                    `<option value="${item.id}">${item.district_name}</option>`);
                $('#districtSelect').html(options);
                $('#citySelect').html('<option value="">-- निवडा --</option>');
            });
        });


    });
</script>
