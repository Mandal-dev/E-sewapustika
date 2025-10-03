        @php
            $designation = Session::get('user.designation_type');
        @endphp
        <!-- Sewapustika Approval Modal / View -->
        <div class="modal-body">
            <!-- Police & Master Data Card -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Police Details</div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>Police Name:</strong> {{ $polices->police_name ?? '--' }}</div>
                        <div class="col-md-6"><strong>Buckle Number:</strong> {{ $polices->buckle_number ?? '--' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>Role / Designation:</strong> {{ $polices->role ?? '--' }}</div>
                        <div class="col-md-6"><strong>Post:</strong> {{ $polices->post ?? '--' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>State:</strong> {{ $polices->state_name ?? '--' }}</div>
                        <div class="col-md-6"><strong>District:</strong> {{ $polices->district_name ?? '--' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6"><strong>City:</strong> {{ $polices->city_name ?? '--' }}</div>
                        <div class="col-md-6"><strong>Police Station:</strong>
                            {{ $polices->police_station_name ?? '--' }}</div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-2">

                            <div class="col-md-6"><strong>Status:</strong> {{ $polices->sewa_pustika_status ?? '--' }}
                            </div>
                            <div class="col-md-6"><strong>Review Status:</strong>
                                {{ $polices->review_status ?? 'Pending' }}</div>
                        </div>
                        <div class="row mb-2">

                            <div class="col-md-6">
                                <strong>Document:</strong>
                                @if ($polices->sewapusticapath)
                                    <a href="{{ route('sewapustika.view', $polices->sewapusticapath) }}"
                                        target="_blank">View / Download</a>
                                @else
                                    Not uploaded
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Form -->
                <form id="sewaPustikaApprovalForm" action="{{ route('sewapustika.approve.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sewapustika_id" value="{{ $polices->sewapustika_id ?? '' }}">

                    <!-- Action -->
                    <div class="mb-3">
                        <label class="form-label">Action</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_approve"
                                value="Approved" required>
                            <label class="form-check-label" for="status_approve">Approve</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="status_reject"
                                value="Rejected">
                            <label class="form-check-label" for="status_reject">Reject</label>
                        </div>
                    </div>

                    <!-- Remark -->
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark (optional)</label>
                        <input type="text" name="remark" id="remark" class="form-control"
                            placeholder="Enter remark (if any)">
                        <small class="form-text text-muted">You can provide a remark for both approval and
                            rejection.</small>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
