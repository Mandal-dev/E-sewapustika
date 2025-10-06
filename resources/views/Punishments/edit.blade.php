@if ($police)
    <div class="modal-header">
        <h5 class="modal-title">{{ __('messages.add_punishment') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <form action="{{ route('punishments.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding-right: 15px;">

            {{-- State and District --}}
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

            {{-- City and Police Name --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.city') }}</label>
                    <input type="text" class="form-control" value="{{ $police->city_name }}" disabled>
                    <input type="hidden" name="station_id" value="{{ $police->city_id }}">
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.police_name') }}</label>
                    <input type="text" class="form-control" value="{{ $police->police_name }}" disabled>
                    <input type="hidden" name="police_id" value="{{ $police->police_user_id }}">
                </div>
            </div>

            {{-- Buckle Number and Punishment Date --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.buckle_number') }}</label>
                    <input type="text" class="form-control" value="{{ $police->buckle_number }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.punishment_date') }}</label>
                    <input type="date" name="punishment_given_date" class="form-control" required>
                </div>
            </div>

            <input type="hidden" name="sewa_pustika_status" value="Uploaded">

            {{-- Punishment Type and Document --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.punishment_type') }}</label>
                    <select name="punishment_type" class="form-control" required>
                        <option value="" disabled selected>{{ __('messages.select_punishment_type') }}</option>
                        <option value="जाचक कार्य">{{ __('messages.type_jachak') }}</option>
                        <option value="वरष्ठ अधिकार्‍यांची समज">{{ __('messages.type_varshtha') }}</option>
                        <option value="सेवेवर परिणाम करणारी शिक्षा">{{ __('messages.type_seve') }}</option>
                        <option value="वेतन कपात">{{ __('messages.type_vetan') }}</option>
                        <option value="इतर">{{ __('messages.type_other') }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.punishment_document') }} (PDF)</label>
                    <input type="file" name="punishment_documents" class="form-control" accept=".pdf" required>
                </div>
            </div>

            {{-- Reason --}}
            <div class="mb-3">
                <label>{{ __('messages.reason_optional') }}</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('messages.enter_punishment_reason') }}"></textarea>
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
