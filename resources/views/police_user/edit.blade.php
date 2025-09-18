<div class="modal-header">
    <h5 class="modal-title fw-bold">
        {{ isset($police) ? 'Police Edit' : 'Police जोडा' }}
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
                <label class="form-label fw-semibold">Police Name</label>
                <input type="text" name="police_name" class="form-control"
                       value="{{ old('police_name', $police->police_name ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Buckle Number</label>
                <input type="text" name="buckle_number" class="form-control"
                       value="{{ old('buckle_number', $police->buckle_number ?? '') }}">
            </div>
        </div>

        <!-- Row 2 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="Male" {{ old('gender', $police->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $police->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $police->gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Mobile</label>
                <input type="text" name="mobile" class="form-control"
                       value="{{ old('mobile', $police->mobile ?? '') }}">
            </div>
        </div>

        <!-- Row 3 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $police->email ?? '') }}">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Religion</label>
                <select name="religion" class="form-select">
                    <option value="">-- Select Religion --</option>
                    @foreach ($religions as $rel)
                        <option value="{{ $rel->id }}"
                            {{ old('religion', $police->religion ?? '') == $rel->id ? 'selected' : '' }}>
                            {{ $rel->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Row 4 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Category</label>
                <select name="caste" id="casteSelect" class="form-select" required>
                    <option value="">-- Category निवडा --</option>
                    <option value="General" {{ old('caste', $police->caste ?? '') == 'General' ? 'selected' : '' }}>General</option>
                    <option value="OBC" {{ old('caste', $police->caste ?? '') == 'OBC' ? 'selected' : '' }}>OBC</option>
                    <option value="SC" {{ old('caste', $police->caste ?? '') == 'SC' ? 'selected' : '' }}>SC</option>
                    <option value="ST" {{ old('caste', $police->caste ?? '') == 'ST' ? 'selected' : '' }}>ST</option>
                    <option value="Others" {{ old('caste', $police->caste ?? '') == 'Others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Sub Caste</label>
                <input type="text" name="category" class="form-control" placeholder="Sub Caste"
                       value="{{ old('category', $police->category ?? '') }}">
            </div>
        </div>

        <!-- Row 5 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">State</label>
                <select name="state_id" class="form-select">
                    <option value="">-- Select State --</option>
                    @foreach ($states as $st)
                        <option value="{{ $st->id }}" {{ old('state_id', $police->state_id ?? '') == $st->id ? 'selected' : '' }}>
                            {{ $st->state_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">District</label>
                <select name="district_id" class="form-select">
                    <option value="">-- Select District --</option>
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
                <label class="form-label fw-semibold">City</label>
                <select name="city_id" class="form-select">
                    <option value="">-- Select City --</option>
                    @foreach ($cities as $c)
                        <option value="{{ $c->id }}" {{ old('city_id', $police->city_id ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->city_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Police Station</label>
                <select name="station_id" class="form-select">
                    <option value="">-- Select Station --</option>
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
                <label class="form-label fw-semibold">Designation</label>
                <select name="designation_id" class="form-select">
                    <option value="">-- Select Designation --</option>
                    @foreach ($designations as $des)
                        <option value="{{ $des->id }}" {{ old('designation_id', $police->designation_id ?? '') == $des->id ? 'selected' : '' }}>
                            {{ $des->designation_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Designation Type</label>
                <select name="designation_type" class="form-select" required>
                    <option value="">-- पद निवडा --</option>
                    <option value="Head_person" {{ old('designation_type', $police->designation_type ?? '') == 'Head_Person' ? 'selected' : '' }}>SP</option>
                    <option value="Station_Head" {{ old('designation_type', $police->designation_type ?? '') == 'Station_Head' ? 'selected' : '' }}>Station Head</option>
                    <option value="Police" {{ old('designation_type', $police->designation_type ?? '') == 'Police' ? 'selected' : '' }}>Police</option>
                </select>
            </div>
        </div>

        <!-- Row 8 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Joining Date</label>
                <input type="date" name="joining_date" class="form-control"
                       value="{{ old('joining_date', $police->joining_date ?? '') }}">
            </div>
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Retirement Date</label>
                <input type="date" name="retirement_date" class="form-control"
                       value="{{ old('retirement_date', $police->retirement_date ?? '') }}">
            </div>
        </div>

        <!-- Row 9 -->
        <div class="row">
            <div class="col-md-12 mb-3 text-start">
                <label class="form-label fw-semibold">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $police->address ?? '') }}</textarea>
            </div>
        </div>

        <!-- Row 10 -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <label class="form-label fw-semibold">Pincode</label>
                <input type="text" name="pincode" class="form-control"
                       value="{{ old('pincode', $police->pincode ?? '') }}">
            </div>
        </div>

    </div>

    <div class="modal-footer">
        <button type="submit" class="btn btn-success">
            {{ isset($police) ? 'Update' : 'Submit' }}
        </button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>
