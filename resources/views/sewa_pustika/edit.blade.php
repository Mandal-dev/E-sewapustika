@if ($police)
    <div class="modal-header">
        <h5 class="modal-title">सेवा पुस्तिका अपडेट करा</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>


    <form action="{{ route('sewa_pustika.save') }}" method="POST" enctype="multipart/form-data">

        @csrf

        <div class="modal-body">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>राज्य</label>
                    <input type="text" class="form-control" value="{{ $police->state_name }}" disabled>
                    <input type="hidden" name="state_id" value="{{ $police->state_id }}">
                </div>
                <div class="col-md-6">
                    <label>जिल्हा</label>
                    <input type="text" class="form-control" value="{{ $police->district_name }}" disabled>
                    <input type="hidden" name="district_id" value="{{ $police->district_id }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>शहर</label>
                    <input type="text" class="form-control" value="{{ $police->name }}" disabled>
                    <input type="hidden" name="city_id" value="{{ $police->station_id }}">
                </div>
                <div class="col-md-6">
                    <label>पोलीस नाव</label>
                    <input type="text" class="form-control" value="{{ $police->police_name }}" disabled>
                    <input type="hidden" name="police_id" value="{{ $police->police_user_id }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>बकल नंबर</label>
                    <input type="text" class="form-control" value="{{ $police->buckle_number }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>सेवा पुस्तिका (PDF)</label>
                    <input type="file" name="sewa_pustika_file" class="form-control" accept=".pdf" required>


                </div>

            </div>
            <input type="hidden" name="sewa_pustika_status" value="Uploaded">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Joining Date</label>
                    <input type="date" name="joining_date" class="form-control" required>
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn btn-primary">सबमिट करा</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करा</button>
        </div>
    </form>
@else
    <div class="alert alert-warning m-3">कोणताही पोलीस सापडला नाही.</div>
@endif
