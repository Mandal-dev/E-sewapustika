<!-- Modal Body Starts -->
<div class="modal-header border-0 pb-0">
    <h5 class="modal-title">
        <i class="fas fa-book-open me-2 text-primary"></i> Add सेवा पुस्तिका पृष्ठ
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <form id="sewaPustikaForm" enctype="multipart/form-data">
        <!-- Officer Dropdown -->
        <div class="mb-3">

            <select class="form-select" name="officer_id" required>
                <option value="">Select Buckle Number</option>
                <option value="">2524</option>
                <option value="">5871</option>
                <option value="">1247</option>
                <option value="">2021</option>
                <option value="">5211</option>
                <option value="">1111</option>

                <!-- Dynamically populate this -->
            </select>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Station Name</label>
                <select class="form-select" name="station_name">
                    <option selected>Nagpur - Central</option>
                    <!-- Add more options dynamically -->
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Police Name</label>
                <input type="text" name="police_name" class="form-control" value="Deshmukh" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Buckle Number</label>
                <input type="text" name="buckle_number" class="form-control" value="458744" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="2000-04-11">
            </div>

            <div class="col-md-4">
                <label class="form-label">Joining Date</label>
                <input type="date" name="joining_date" class="form-control" value="2025-07-11">
            </div>

            <div class="col-md-4">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone_number" class="form-control" value="8767219765">
            </div>

            <div class="col-md-4">
                <label class="form-label">Gender</label>
                <select class="form-select" name="gender">
                    <option selected>Male</option>
                    <option>Female</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Blood Group</label>
                <select class="form-select" name="blood_group">
                    <option selected>AB+</option>
                    <!-- Add more options -->
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Email ID</label>
                <input type="email" name="email" class="form-control" value="xyz@gmail.com">
            </div>

            <div class="col-md-12">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="Reshimbagh Road Nagour - 4403254">
            </div>

            <div class="col-md-12">
                <label class="form-label">Upload Add सेवा पुस्तिका पृष्ठ</label>
                <div class="input-group">
                    <input type="file" class="form-control" name="pustika_file" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <small class="text-muted">Please upload only PDF or image files (.pdf, .jpg)</small>
                <div class="text-danger mt-1" style="font-size: 0.9rem;">*No File Chosen</div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer border-0">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
    <button type="submit" form="sewaPustikaForm" class="btn btn-success">Save</button>
</div>
<!-- Modal Body Ends -->
