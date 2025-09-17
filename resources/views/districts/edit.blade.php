<!-- Edit District Form -->
<div class="modal-header">
    <h5 class="modal-title">Edit District</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form action="{{ route('districts.update', $district->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="modal-body">
        <div class="mb-3">
            <label>Country ID</label>
            <input type="number" name="country_id" value="{{ $district->country_id }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>State ID</label>
            <input type="number" name="state_id" value="{{ $district->state_id }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>District Name</label>
            <input type="text" name="district_name" value="{{ $district->district_name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>District Name (Marathi)</label>
            <input type="text" name="district_name_marathi" value="{{ $district->district_name_marathi }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>District Name (Hindi)</label>
            <input type="text" name="district_name_hindi" value="{{ $district->district_name_hindi }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Active" {{ $district->status == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ $district->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary">Update</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    </div>
</form>
