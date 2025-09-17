@if ($police)
    <div class="modal-header">
        <h5 class="modal-title">शिक्षा अपडेट करा</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <form action="{{ route('punishments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-body">

            {{-- राज्य आणि जिल्हा --}}
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

            {{-- शहर आणि पोलीस नाव --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>शहर</label>
                    <input type="text" class="form-control" value="{{ $police->city_name }}" disabled>
                    <input type="hidden" name="station_id" value="{{ $police->city_id }}">
                </div>
                <div class="col-md-6">
                    <label>पोलीस नाव</label>
                    <input type="text" class="form-control" value="{{ $police->police_name }}" disabled>
                    <input type="hidden" name="police_id" value="{{ $police->police_user_id }}">
                </div>
            </div>

            {{-- बकल नंबर आणि सेवा पुस्तिका --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>बकल नंबर</label>
                    <input type="text" class="form-control" value="{{ $police->buckle_number }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>शिक्षेची तारीख</label>
                    <input type="date" name="punishment_given_date" class="form-control" required>
                </div>
            </div>

            <input type="hidden" name="sewa_pustika_status" value="Uploaded">

            {{-- शिक्षा दस्तऐवज --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>शिक्षेचा प्रकार</label>
                    <select name="punishment_type" class="form-control" required>
                        <option value="" disabled selected>शिक्षेचा प्रकार निवडा</option>
                        <option value="जाचक कार्य">जाचक कार्य</option>
                        <option value="वरष्ठ अधिकार्‍यांची समज">वरष्ठ अधिकार्‍यांची समज</option>
                        <option value="सेवेवर परिणाम करणारी शिक्षा">सेवेवर परिणाम करणारी शिक्षा</option>
                        <option value="वेतन कपात">वेतन कपात</option>
                        <option value="इतर">इतर</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>शिक्षेचा दस्तऐवज (PDF)</label>
                    <input type="file" name="punishment_documents" class="form-control" accept=".pdf" required>
                </div>
            </div>

            {{-- कारण --}}
            <div class="mb-3">
                <label>कारण (ऐच्छिक)</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="शिक्षेचे कारण लिहा..."></textarea>
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
