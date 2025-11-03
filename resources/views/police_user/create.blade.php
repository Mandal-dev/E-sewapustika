<div class="modal-header">
    <h5 class="modal-title fw-bold">
        {{ isset($police) ? __('messages.update') : __('messages.add_police') }}
    </h5>
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

    <div class="modal-body" style="max-height:70vh; overflow-y:auto; padding: 1.5rem; background-color: #fff; position: relative;">
        <!-- Country & State -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.country_name') }} <span class="text-danger">*</span></label>
                <select name="country_id" id="countrySelect" class="form-select" required>
                    <option value="">{{ __('messages.select_country') }}</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.state_name') }} <span class="text-danger">*</span></label>
                <select name="state_id" id="stateSelect" class="form-select" required>
                    <option value="">{{ __('messages.select_state') }}</option>
                </select>
            </div>
        </div>

        <!-- District & City -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.district') }} <span class="text-danger">*</span></label>
                <select name="district_id" id="districtSelect" class="form-select" required>
                    <option value="">{{ __('messages.select_district') }}</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.city') }} <span class="text-danger">*</span></label>
                <select name="city_id" id="citySelect" class="form-select" required>
                    <option value="">{{ __('messages.select_city') }}</option>
                </select>
            </div>
        </div>

        <!-- Police Station & Gender -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.station_name') }} <span class="text-danger">*</span></label>
                <select name="station_id" id="stationSelect" class="form-select" required>
                    <option value="">{{ __('messages.select_station') }}</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.gender') }} <span class="text-danger">*</span></label>
                <select name="gender" class="form-select" required>
                    <option value="">{{ __('messages.select_gender') }}</option>
                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>{{ __('messages.male') }}</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ __('messages.female') }}</option>
                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>{{ __('messages.other') }}</option>
                </select>
                @error('gender')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Police Name & Email -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.police_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="police_name" class="form-control" placeholder="{{ __('messages.police_name') }}" required>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.email') }}</label>
                <input type="email" name="email" class="form-control" placeholder="{{ __('messages.email') }}">
            </div>
        </div>

        <!-- Mobile & Buckle Number -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.mobile_no') }}</label>
                <input type="text" name="mobile" class="form-control" maxlength="10" placeholder="{{ __('messages.mobile_no') }}">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.buckle_number') }}</label>
                <input type="text" name="buckle_number" class="form-control" placeholder="{{ __('messages.buckle_number') }}">
            </div>
        </div>

        <!-- Designation & Type -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.designation') }}</label>
                <select name="designation_id" class="form-select">
                    <option value="">{{ __('messages.select_designation') }}</option>
                    @foreach ($designations as $desig)
                        <option value="{{ $desig->id }}">{{ $desig->designation_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.designation_type') }}</label>
                <select name="designation_type" class="form-select">
                    <option value="">{{ __('messages.select_designation_type') }}</option>
                    <option value="Head_Person">SP</option>
                    <option value="Station_Head">Station Head</option>
                    <option value="Police">Police</option>
                    <option value="Account_Department">Account Department</option>
                    <option value="Rewards_Department">Rewards Department</option>
                    <option value="Sewapustika_Department">Sewapustika Department</option>
                    <option value="Punishment_Department">Punishment Department</option>
                    <option value="Leave_Department">Leave Department</option>

                </select>
            </div>
        </div>

        <!-- Religion, Category, Sub-caste -->
        <div class="row">
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.religion') }}</label>
                <select name="religion" id="religionSelect" class="form-select">
                    <option value="">{{ __('messages.select_religion') }}</option>
                    @foreach ($religions as $religion)
                        <option value="{{ $religion->id }}">{{ $religion->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.category') }}</label>
                <select name="caste" id="casteSelect" class="form-select">
                    <option value="">{{ __('messages.select_category') }}</option>
                    <option value="General">General</option>
                    <option value="OBC">OBC</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                    <option value="Others">Others</option>
                </select>
            </div>
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.sub_caste') }}</label>
                <input type="text" name="sub_caste" class="form-control" placeholder="{{ __('messages.sub_caste') }}">
            </div>
        </div>

        <!-- Joining & Retirement Dates -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.joining_date') }}</label>
                <input type="date" name="joining_date" class="form-control">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.retirement_date') }}</label>
                <input type="date" name="retirement_date" class="form-control">
            </div>
        </div>

        <!-- Address & Pincode -->
        <div class="row">
            <div class="col-md-8 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.address') }}</label>
                <input type="text" name="address" class="form-control" placeholder="{{ __('messages.address') }}">
            </div>
            <div class="col-md-4 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.pincode') }}</label>
                <input type="text" name="pincode" class="form-control" placeholder="{{ __('messages.pincode') }}">
            </div>
        </div>
    </div>

    <div class="modal-footer d-flex justify-content-end gap-2">
        <button type="submit" class="btn btn-success px-4">{{ isset($police) ? __('messages.update') : __('messages.submit') }}</button>
        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Country → State
        $('#countrySelect').change(function() {
            let countryId = $(this).val();
            $('#stateSelect').html('<option>{{ __('messages.loading') }}...</option>');
            $('#districtSelect, #citySelect').html('<option value="">{{ __('messages.select') }}</option>');
            if (countryId) {
                $.get(`/states/by-country/${countryId}`, function(data) {
                    let options = `<option value="">{{ __('messages.select_state') }}</option>`;
                    data.forEach(item => options += `<option value="${item.id}">${item.state_name}</option>`);
                    $('#stateSelect').html(options);
                });
            }
        });

        // State → District
        $('#stateSelect').change(function() {
            let stateId = $(this).val();
            $('#districtSelect').html('<option>{{ __('messages.loading') }}...</option>');
            $('#citySelect').html('<option value="">{{ __('messages.select_city') }}</option>');
            if (stateId) {
                $.get(`/districts/by-state/${stateId}`, function(data) {
                    let options = `<option value="">{{ __('messages.select_district') }}</option>`;
                    data.forEach(item => options += `<option value="${item.id}">${item.district_name}</option>`);
                    $('#districtSelect').html(options);
                });
            }
        });

        // District → City
        $('#districtSelect').change(function() {
            let districtId = $(this).val();
            $('#citySelect').html('<option>{{ __('messages.loading') }}...</option>');
            if (districtId) {
                $.get(`/cities/by-district/${districtId}`, function(data) {
                    let options = `<option value="">{{ __('messages.select_city') }}</option>`;
                    data.forEach(item => options += `<option value="${item.id}">${item.city_name}</option>`);
                    $('#citySelect').html(options);
                });
            }
        });

        // City → Station
        $('#citySelect').change(function() {
            let cityId = $(this).val();
            $('#stationSelect').html('<option>{{ __('messages.loading') }}...</option>');
            if (cityId) {
                $.get(`/stations/by-city/${cityId}`, function(data) {
                    let options = `<option value="">{{ __('messages.select_station') }}</option>`;
                    data.forEach(item => options += `<option value="${item.id}">${item.name}</option>`);
                    $('#stationSelect').html(options);
                });
            }
        });
    });
</script>
