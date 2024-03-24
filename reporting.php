<?php include_once "commonfiles/header.php"; ?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center">
        <h2>User Management</h2>
        <div class="form-inline">
            <div class="input-group mr-3">
                <input id="searchInput" type="text" class="form-control" placeholder="Search">
                <div class="input-group-append">
                    <button id="searchBtn" class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <!-- <button id="addUserBtn" class="btn btn-success" data-toggle="modal" data-target="#addUserModal"><i class="fas fa-plus"></i> Add User</button> -->
        </div>
    </div>

    <table id="userTable" class="table table-bordered mt-3">

        <thead class="thead-dark">
            <tr>


                <th scope="col">Full Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Report Type</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include_once "db_connection.php";


            $sql = "
            select appt.appoinment_type , us.full_name  , us.email , us.phone_number , us.user_id
            from appoinment_type appt
            join appointments ap on appt.app_id = ap.app_id
            join users us on us.user_id = ap.patient_id
            where ap.status ='paid'";
            $result = mysqli_query($conn, $sql);


            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

                    echo "<tr>";


                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['phone_number'] . "</td>";
                    echo "<td>" . $row['appoinment_type'] . "</td>";
                    echo "<td>";
                    echo "<a href='#' class='btn btn-sm btn-info addReportBtn mr-2' data-toggle='modal' data-target='#addReportModal' data-user-id='" . $row['user_id'] . "'><i class='fas fa-plus-circle'></i> Add</a>";

                    echo "<a href='#' class='btn btn-sm btn-warning editUserBtn' data-toggle='modal' data-target='#editReportModal' data-user-id='" . $row['user_id'] . "'><i class='fas fa-pencil-alt'></i> Edit</a>";




                    echo "</td>";
                    echo "</tr>";
                }
            } else {

                echo "<tr><td colspan='9'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<!-- Add Report Modal -->
<div class="modal fade" id="addReportModal" tabindex="-1" aria-labelledby="addReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReportModalLabel">Add Test Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addReportForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="patient_id">Patient ID</label>
                        <input type="text" readonly class="form-control" id="patient_id" name="patient_id" required>
                    </div>
                    <div class="form-group">
                        <label for="report_name">Report Name</label>
                        <input type="text" class="form-control" id="report_name" name="report_name" required>
                    </div>
                    <div class="form-group">
                        <label for="report_date">Report Date</label>
                        <input type="date" class="form-control" id="report_date" name="report_date" required>
                    </div>
                    <div class="form-group">
                        <label for="report_file">Report File</label>
                        <input type="file" class="form-control-file" id="report_file" name="report_file" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload Report</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Report Modal -->
<div class="modal fade" id="editReportModal" tabindex="-1" aria-labelledby="editReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReportModalLabel">Edit Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editReportForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_report_id" name="report_id">
                    <div class="form-group">
                        <label for="edit_report_name">Report Name</label>
                        <input type="text" class="form-control" id="edit_report_name" name="report_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_report_date">Report Date</label>
                        <input type="date" class="form-control" id="edit_report_date" name="report_date" required>
                    </div>
                    <div class="form-group">
                        <label for="report_file">Report File</label>
                        <input type="file" class="form-control-file" id="report_file" name="report_file" required>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>





<?php include_once "commonfiles/script.php"; ?>

<script>
    $(document).ready(function() {
        $('.addReportBtn').on('click', function() {
            var userId = $(this).data('user-id');

            $.ajax({
                url: 'getUserDetails.php',
                type: 'GET',
                data: {
                    'user_id': userId
                },
                dataType: 'json',
                success: function(data) {

                    $('#patient_id').val(data.user_id);

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('AJAX error:', textStatus, errorThrown);
                }
            });
        });
    });

    $(document).ready(function() {
        $('#addReportForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'upload_report.php',
                type: 'POST',
                data: formData,
                contentType: false, // Important for sending files
                processData: false, // Important for sending files
                success: function(response) {

                    console.log(response);
                    alert("Report uploaded successfully.");
                    $('#addReportModal').modal('hide');
                    location.reload();
                },
                error: function(xhr, status, error) {

                    console.error("Error during AJAX request:", status, error);
                    alert("An error occurred while uploading the report.");
                }
            });



        });
    });

    $(document).on('click', '.editUserBtn', function() {
        var userId = $(this).data('user-id');
        $.ajax({
            url: 'fetch_report_details.php',
            type: 'GET',
            data: {
                'user_id': userId
            },
            dataType: 'json',
            success: function(response) {
                // Use 'response.data' to access the report details
                $('#edit_report_id').val(response.data.report_id);
                $('#edit_report_name').val(response.data.report_name);

                var formattedDate = response.data.report_date.split(' ')[0]; // Assuming 'report_date' is in 'YYYY-MM-DD HH:MM:SS' format
                $('#edit_report_date').val(formattedDate);

                // Show the modal
                $('#editReportModal').modal('show');
            }
        });
    });

    $(document).ready(function() {
        $('#editReportForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            var formData = new FormData(this); // Create a FormData object, passing in the form

            $.ajax({
                url: 'update_report.php', // The PHP file that will update the report in the database
                type: 'POST',
                data: formData,
                contentType: false, // Required for FormData
                processData: false, // Required for FormData
                success: function(response) {
                    // Handle success (you can parse JSON response if you're returning JSON from PHP)
                    alert('Report updated successfully!');
                    $('#editReportModal').modal('hide'); // Close the modal
                    location.reload();
                    // Optionally, refresh part of your page or re-fetch reports to reflect the update
                },
                error: function() {
                    // Handle error
                    alert('An error occurred while updating the report.');
                }
            });
        });
    });

    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#userTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>