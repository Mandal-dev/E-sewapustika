@if ($police)
    <div class="modal-header">
        <h5 class="modal-title">{{ __('messages.update_sewa_pustika') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <form action="{{ route('sewa_pustika.save') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.state') }}</label>
                    <input type="text" class="form-control" value="{{ $police->state_name }}" disabled>
                    <input type="hidden" name="state_id" value="{{ $police->state_id }}">
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.district') }}</label>
                    <input type="text" class="form-control" value="{{ $police->district_name }}" disabled>
                    <input type="hidden" name="district_id" value="{{ $police->district_id }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.city') }}</label>
                    <input type="text" class="form-control" value="{{ $police->name }}" disabled>
                    <input type="hidden" name="city_id" value="{{ $police->station_id }}">
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.police_name') }}</label>
                    <input type="text" class="form-control" value="{{ $police->police_name }}" disabled>
                    <input type="hidden" name="police_id" value="{{ $police->police_user_id }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.buckle_number') }}</label>
                    <input type="text" class="form-control" value="{{ $police->buckle_number }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.sewa_pustika_pdf') }}</label>
                    <input type="file" name="sewa_pustika_file" class="form-control" accept=".pdf" required>
                </div>
            </div>

            <input type="hidden" name="sewa_pustika_status" value="Uploaded">

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.joining_date') }}</label>
                    <input type="date" name="joining_date" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn btn-primary">{{ __('messages.submit') }}</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
        </div>
    </form>
@else
    <div class="alert alert-warning m-3">{{ __('messages.no_police_found') }}</div>
@endif
