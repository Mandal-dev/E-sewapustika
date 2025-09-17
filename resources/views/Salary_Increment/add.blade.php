<div class="modal-content">
    <!-- Fixed Header -->
    <div class="modal-header">
        <h5 class="modal-title">{{ __('messages.salary_increment_upload') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <form action="{{ route('salary.increment.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Scrollable Body -->
        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding-right: 15px;">

            {{-- Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

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

            {{-- Buckle No & Increment Date --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.buckle_number') }}</label>
                    <input type="text" class="form-control" value="{{ $police->buckle_number }}" disabled>
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.increment_date') }}</label>
                    <input type="date" name="increment_date" class="form-control" required>
                </div>
            </div>

            {{-- Increment Type & Grade Pay --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.increment_type') }}</label>
                    <select name="increment_type" class="form-control" required>
                        <option value="" disabled selected>{{ __('messages.increment_type') }}</option>
                        <option value="दरवाढ">{{ __('messages.type_increment') }}</option>
                        <option value="पदोन्नती">{{ __('messages.type_promotion') }}</option>
                        <option value="इतर">{{ __('messages.type_other') }}</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.grade_pay') }}</label>
                    <select id="grade_pay_select" name="grade_pay" class="form-control">
                        <option value="">{{ __('messages.select_grade_pay') }}</option>
                        @foreach ($grade_pay_options as $option)
                            <option value="{{ $option->id }}"
                                {{ old('grade_pay', $police->grade_pay_id ?? '') == $option->id ? 'selected' : '' }}>
                                {{ $option->stage_code }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Level & Net Salary --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.level_no') }}</label>
                    <select id="level_no_select" name="level_no" class="form-control">
                        <option value="">{{ __('messages.select_level') }}</option>
                        @foreach ($pay_lavels_options as $option)
                            <option value="{{ $option->id}}"
                                {{ old('level_no', $police->level_id ?? '') == $option->id ? 'selected' : '' }}>
                                {{ $option->level_no }}
                            </option>
                        @endforeach

                    </select>

                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.net_salary') }}</label>
                    <input type="number" step="0.01" name="new_salary" class="form-control">
                </div>
            </div>

            {{-- Increased Amount & Document --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>{{ __('messages.increased_amount') }}</label>
                    <input type="number" step="0.01" name="increased_amount" class="form-control">
                </div>
                <div class="col-md-6">
                    <label>{{ __('messages.increment_documents') }}</label>
                    <input type="file" name="increment_documents" class="form-control" accept=".pdf" required>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">{{ __('messages.submit') }}</button>
            <button type="button" class="btn btn-secondary"
                data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
        </div>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function fetchSalary() {
            let level_no = $('#level_no_select').val();
            let grade_pay = $('#grade_pay_select').val();

            if (level_no && grade_pay) {
                $.ajax({
                    url: "{{ route('get.salary') }}",
                    type: "GET",
                    data: {
                        level_no: level_no,
                        grade_pay: grade_pay
                    },
                    success: function(response) {
                        if (response.salary) {
                            $('input[name="new_salary"]').val(response.salary);
                        } else {
                            $('input[name="new_salary"]').val('');
                            alert("No salary found for selected Level & Grade Pay");
                        }
                    },
                    error: function() {
                        alert("Error fetching salary");
                    }
                });
            }
        }

        $('#level_no_select, #grade_pay_select').change(fetchSalary);
    });
</script>
