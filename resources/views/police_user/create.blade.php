<div class="modal-header">
    <h5 class="modal-title fw-bold">Police जोडा</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('police.store') }}" method="POST">
    @csrf

    <div class="modal-body"
        style="max-height:70vh; overflow-y:auto; padding: 1.5rem; background-color: #fff; position: relative;">
        <!-- Country & State -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">देशाचे नाव <span class="text-danger">*</span></label>
                <select name="country_id" id="countrySelect" class="form-select" required>
                    <option value="">-- देश निवडा --</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">राज्याचे नाव <span class="text-danger">*</span></label>
                <select name="state_id" id="stateSelect" class="form-select" required>
                    <option value="">-- राज्य निवडा --</option>
                </select>
            </div>
        </div>

        <!-- District & City -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">जिल्हा <span class="text-danger">*</span></label>
                <select name="district_id" id="districtSelect" class="form-select" required>
                    <option value="">-- जिल्हा निवडा --</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">शहर <span class="text-danger">*</span></label>
                <select name="city_id" id="citySelect" class="form-select" required>
                    <option value="">-- शहर निवडा --</option>
                </select>
            </div>
        </div>

        <!-- Police Station -->



        <!-- Gender -->
        <div class="row">
                    <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">ठाणे नाव <span class="text-danger">*</span></label>
                <select name="station_id" id="stationSelect" class="form-select" required>
                    <option value="">-- ठाणे निवडा --</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">लिंग <span class="text-danger">*</span></label>
                <select name="gender" class="form-select" required>
                    <option value="">-- लिंग निवडा --</option>
                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>पुरुष</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>महिला</option>
                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>इतर</option>
                </select>
                @error('gender')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <!-- Police Name & Email -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">पोलीस नाव <span class="text-danger">*</span></label>
                <input type="text" name="police_name" class="form-control" placeholder="पोलीस नाव" required>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">ईमेल</label>
                <input type="email" name="email" class="form-control" placeholder="example@email.com">
            </div>
        </div>

        <!-- Mobile & Buckle Number -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">मोबाईल नंबर</label>
                <input type="text" name="mobile" class="form-control" maxlength="10" placeholder="मोबाईल नंबर">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">बकल नंबर</label>
                <input type="text" name="buckle_number" class="form-control" placeholder="बकल नंबर">
            </div>
        </div>

        <!-- Designation & Type -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">पद</label>
                <select name="designation_id" class="form-select">
                    <option value="">-- पद निवडा --</option>
                    @foreach ($designations as $desig)
                        <option value="{{ $desig->id }}">{{ $desig->designation_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Designation Type</label>
                <select name="designation_type" class="form-select">
                    <option value="">-- पद निवडा --</option>
                    <option value="Head_Person">SP</option>
                    <option value="Station_Head">Station Head</option>
                    <option value="Police">Police</option>
                </select>
            </div>
        </div>

        <!-- Religion, Category, Sub-caste -->
        <div class="row">
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">Religion</label>
                <select name="religion" id="religionSelect" class="form-select">
                    <option value="">-- Religion निवडा --</option>
                    @foreach ($religions as $religion)
                        <option value="{{ $religion->id }}">{{ $religion->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">Category</label>
                <select name="caste" id="casteSelect" class="form-select">
                    <option value="">-- Category निवडा --</option>
                    <option value="General">General</option>
                    <option value="OBC">OBC</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">Sub Caste</label>
                <input type="text" name="sub_caste" class="form-control" placeholder="Sub Caste">
            </div>
        </div>

        <!-- Joining & Retirement Dates -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Joining Date</label>
                <input type="date" name="joining_date" class="form-control">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Retirement Date</label>
                <input type="date" name="retirement_date" class="form-control">
            </div>
        </div>

        <!-- Address & Pincode -->
        <div class="row">
            <div class="col-md-8 mb-3 text-start">
                <label class="form-label fw-semibold">Address</label>
                <input type="text" name="address" class="form-control" placeholder="Address">
            </div>
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">Pincode</label>
                <input type="text" name="pincode" class="form-control" placeholder="Pincode">
            </div>
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
        $('#countrySelect').change(function() {
            let countryId = $(this).val();
            $('#stateSelect').html('<option>लोड करत आहे...</option>');
            $('#districtSelect, #citySelect').html('<option value="">-- निवडा --</option>');
            if (countryId) {
                $.get(`/states/by-country/${countryId}`, function(data) {
                    let options = '<option value="">-- राज्य निवडा --</option>';
                    data.forEach(item => options +=
                        `<option value="${item.id}">${item.state_name}</option>`);
                    $('#stateSelect').html(options);
                });
            }
        });

        // State → District
        $('#stateSelect').change(function() {
            let stateId = $(this).val();
            $('#districtSelect').html('<option>लोड करत आहे...</option>');
            $('#citySelect').html('<option value="">-- शहर निवडा --</option>');
            if (stateId) {
                $.get(`/districts/by-state/${stateId}`, function(data) {
                    let options = '<option value="">-- जिल्हा निवडा --</option>';
                    data.forEach(item => options +=
                        `<option value="${item.id}">${item.district_name}</option>`);
                    $('#districtSelect').html(options);
                });
            }
        });

        // District → City
        $('#districtSelect').change(function() {
            let districtId = $(this).val();
            $('#citySelect').html('<option>लोड करत आहे...</option>');
            if (districtId) {
                $.get(`/cities/by-district/${districtId}`, function(data) {
                    let options = '<option value="">-- शहर निवडा --</option>';
                    data.forEach(item => options +=
                        `<option value="${item.id}">${item.city_name}</option>`);
                    $('#citySelect').html(options);
                });
            }
        });

        // City → Station
        $('#citySelect').change(function() {
            let cityId = $(this).val();
            $('#stationSelect').html('<option>लोड करत आहे...</option>');
            if (cityId) {
                $.get(`/stations/by-city/${cityId}`, function(data) {
                    let options = '<option value="">-- ठाणे निवडा --</option>';
                    data.forEach(item => options +=
                        `<option value="${item.id}">${item.name}</option>`);
                    $('#stationSelect').html(options);
                });
            }
        });
    });
</script>
