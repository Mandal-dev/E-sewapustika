@if ($police)
    <div class="modal-header">
        <h5 class="modal-title">{{ __('messages.upload_reward') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <form action="{{ route('rewards.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding-right: 15px;">

            {{-- State & District --}}
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

            {{-- City & Police Name --}}
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

            {{-- Buckle Number & Reward Date --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.buckle_number') }}</label>
                    <input type="text" class="form-control" value="{{ $police->buckle_number }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.reward_date') }}</label>
                    <input type="date" name="reward_given_date" class="form-control" required>
                </div>
            </div>

            {{-- Reward Document --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.reward_document') }} (PDF)</label>
                    <input type="file" name="rewards_documents" class="form-control" accept=".pdf" required>
                </div>

                {{-- Reward Type --}}
                <div class="col-md-6">
                    <label>{{ __('messages.reward_type') }}</label>
                    <select name="reward_type" class="form-control" required>
                        <option value="" disabled selected>{{ __('messages.select_reward') }}</option>
                        <option value="Certificate of Appreciation">{{ __('messages.certificate_of_appreciation') }}</option>
                        <option value="Cash Reward">{{ __('messages.cash_reward') }}</option>
                        <option value="Medal">{{ __('messages.medal') }}</option>
                        <option value="Other">{{ __('messages.other') }}</option>
                    </select>
                </div>
            </div>

            {{-- Reason --}}
            <div class="mb-3">
                <label>{{ __('messages.reason_optional') }}</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('messages.reward_reason_placeholder') }}"></textarea>
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
