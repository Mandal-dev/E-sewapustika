<div class="modal-header">
    <h5 class="modal-title fw-bold">
        {{ isset($police) ? __('messages.edit_profile') : __('messages.add_police') }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form action="{{ isset($police) ? route('police.update', $police->id) : route('police.store') }}" method="POST">
    @csrf
    @if (isset($police))
        @method('PUT')
    @endif

    <div class="modal-body" style="max-height:70vh; overflow-y:auto;">

        <!-- Row 1 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.police_name') }}</label>
                <input type="text" name="police_name" class="form-control"
                    value="{{ old('police_name', $police->police_name ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.buckle_number') }}</label>
                <input type="text" name="buckle_number" class="form-control"
                    value="{{ old('buckle_number', $police->buckle_number ?? '') }}">
            </div>
        </div>

        <!-- Row 2 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.gender') }}</label>
                <select name="gender" class="form-select" required>
                    <option value="">{{ __('messages.select_gender') }}</option>
                    <option value="Male" {{ old('gender', $police->gender ?? '') == 'Male' ? 'selected' : '' }}>
                        {{ __('messages.male') }}
                    </option>
                    <option value="Female" {{ old('gender', $police->gender ?? '') == 'Female' ? 'selected' : '' }}>
                        {{ __('messages.female') }}
                    </option>
                    <option value="Other" {{ old('gender', $police->gender ?? '') == 'Other' ? 'selected' : '' }}>
                        {{ __('messages.other') }}
                    </option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.mobile_no') }}</label>
                <input type="text" name="mobile" class="form-control"
                    value="{{ old('mobile', $police->mobile ?? '') }}">
            </div>
        </div>

        <!-- Row 3 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.email') }}</label>
                <input type="email" name="email" class="form-control"
                    value="{{ old('email', $police->email ?? '') }}">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.religion') }}</label>
                <select name="religion" class="form-select">
                    <option value="">{{ __('messages.select_religion') }}</option>
                    @foreach ($religions as $rel)
                        <option value="{{ $rel->id }}" {{ old('religion', $police->religion ?? '') == $rel->id ? 'selected' : '' }}>
                            {{ $rel->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 4 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.category') }}</label>
                <select name="caste" id="casteSelect" class="form-select" required>
                    <option value="">{{ __('messages.select_category') }}</option>
                    <option value="General" {{ old('caste', $police->caste ?? '') == 'General' ? 'selected' : '' }}>
                        {{ __('messages.general') }}</option>
                    <option value="OBC" {{ old('caste', $police->caste ?? '') == 'OBC' ? 'selected' : '' }}>
                        {{ __('messages.obc') }}</option>
                    <option value="SC" {{ old('caste', $police->caste ?? '') == 'SC' ? 'selected' : '' }}>
                        {{ __('messages.sc') }}</option>
                    <option value="ST" {{ old('caste', $police->caste ?? '') == 'ST' ? 'selected' : '' }}>
                        {{ __('messages.st') }}</option>
                    <option value="Others" {{ old('caste', $police->caste ?? '') == 'Others' ? 'selected' : '' }}>
                        {{ __('messages.others') }}</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.sub_caste') }}</label>
                <input type="text" name="category" class="form-control" placeholder="{{ __('messages.sub_caste') }}"
                    value="{{ old('category', $police->category ?? '') }}">
            </div>
        </div>

        <!-- Row 5 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.state') }}</label>
                <select name="state_id" class="form-select">
                    <option value="">{{ __('messages.select_state') }}</option>
                    @foreach ($states as $st)
                        <option value="{{ $st->id }}" {{ old('state_id', $police->state_id ?? '') == $st->id ? 'selected' : '' }}>
                            {{ $st->state_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.district') }}</label>
                <select name="district_id" class="form-select">
                    <option value="">{{ __('messages.select_district') }}</option>
                    @foreach ($districts as $d)
                        <option value="{{ $d->id }}" {{ old('district_id', $police->district_id ?? '') == $d->id ? 'selected' : '' }}>
                            {{ $d->district_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 6 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.city') }}</label>
                <select name="city_id" class="form-select">
                    <option value="">{{ __('messages.select_city') }}</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c->id }}" {{ old('city_id', $police->city_id ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->city_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.police_station') }}</label>
                <select name="station_id" class="form-select">
                    <option value="">{{ __('messages.select_station') }}</option>
                    @foreach ($stations as $ps)
                        <option value="{{ $ps->id }}" {{ old('station_id', $police->police_station_id ?? '') == $ps->id ? 'selected' : '' }}>
                            {{ $ps->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 7 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.designation') }}</label>
                <select name="designation_id" class="form-select">
                    <option value="">{{ __('messages.select_designation') }}</option>
                    @foreach ($designations as $des)
                        <option value="{{ $des->id }}" {{ old('designation_id', $police->designation_id ?? '') == $des->id ? 'selected' : '' }}>
                            {{ $des->designation_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.designation_type') }}</label>
                <select name="designation_type" class="form-select" required>
                    <option value="">{{ __('messages.select_designation_type') }}</option>
                    <option value="Head_Person" {{ old('designation_type', $police->designation_type ?? '') == 'Head_Person' ? 'selected' : '' }}>
                        {{ __('messages.sp') }}</option>
                    <option value="Station_Head" {{ old('designation_type', $police->designation_type ?? '') == 'Station_Head' ? 'selected' : '' }}>
                        {{ __('messages.station_head') }}</option>
                    <option value="Police" {{ old('designation_type', $police->designation_type ?? '') == 'Police' ? 'selected' : '' }}>
                        {{ __('messages.police') }}</option>
                    <option value="Account_Department" {{ old('designation_type', $police->designation_type ?? '') == 'Account_Department' ? 'selected' : '' }}>
                        {{ __('messages.account_department') }}</option>
                    <option value="Rewards_Department" {{ old('designation_type', $police->designation_type ?? '') == 'Rewards_Department' ? 'selected' : '' }}>
                        {{ __('messages.rewards_department') }}</option>
                    <option value="Sewapustika_Department" {{ old('designation_type', $police->designation_type ?? '') == 'Sewapustika_Department' ? 'selected' : '' }}>
                        {{ __('messages.sewapustika_department') }}</option>
                    <option value="Punishment_Department" {{ old('designation_type', $police->designation_type ?? '') == 'Punishment_Department' ? 'selected' : '' }}>
                        {{ __('messages.punishment_department') }}</option>
                        <option value="Leave_Department">Leave Department</option>
                </select>
            </div>
        </div>

        <!-- Row 8 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.joining_date') }}</label>
                <input type="date" name="joining_date" class="form-control"
                    value="{{ old('joining_date', $police->joining_date ?? '') }}">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.retirement_date') }}</label>
                <input type="date" name="retirement_date" class="form-control"
                    value="{{ old('retirement_date', $police->retirement_date ?? '') }}">
            </div>
        </div>

        <!-- Row 9 -->
        <div class="row">
            <div class="col-md-12 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.address') }}</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $police->address ?? '') }}</textarea>
            </div>
        </div>

        <!-- Row 10 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">{{ __('messages.pincode') }}</label>
                <input type="text" name="pincode" class="form-control"
                    value="{{ old('pincode', $police->pincode ?? '') }}">
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-success">
            {{ isset($police) ? __('messages.update') : __('messages.submit') }}
        </button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
    </div>
</form>
