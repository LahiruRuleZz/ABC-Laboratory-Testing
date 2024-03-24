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
            <button id="addUserBtn" class="btn btn-success" data-toggle="modal" data-target="#addUserModal"><i class="fas fa-plus"></i> Add User</button>
        </div>
    </div>

    <table id="userTable" class="table table-bordered mt-3">

        <thead class="thead-dark">
            <tr>
                <th scope="col">User ID</th>
                <th scope="col">Username</th>
                <th scope="col">User Type</th>
                <th scope="col">Full Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Date of Birth</th>
                <th scope="col">Created At</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include_once "db_connection.php";


            $sql = "SELECT * FROM users";
            $result = mysqli_query($conn, $sql);


            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

                    echo "<tr>";
                    echo "<td>" . $row['user_id'] . "</td>";
                    echo "<td>" . $row['username'] . "</td>";
                    echo "<td>" . $row['user_type'] . "</td>";
                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['phone_number'] . "</td>";
                    echo "<td>" . $row['date_of_birth'] . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>";
                    echo "<a href='#' class='btn btn-sm btn-info viewUserBtn' data-user-id='" . $row['user_id'] . "'><i class='fas fa-eye'></i> View</a>";
                    echo "<a href='#' class='btn btn-sm btn-primary editUserBtn' data-user-id='" . $row['user_id'] . "'><i class='fas fa-edit'></i> Edit</a>";
                    echo "<a href='#' class='btn btn-sm btn-danger deleteUserBtn' data-user-id='" . $row['user_id'] . "'><i class='fas fa-trash'></i> Delete</a>";

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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="user_type">User Type</label>
                        <select class="form-control" id="user_type" name="user_type" required>
                            <option value="">Select User Type</option>
                            <option value="admin">Admin</option>
                            <option value="technician">Technician</option>
                            <option value="receptionist">Receptionist</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                    </div>

                    <input type="hidden" id="user_id" name="user_id" value="">
                    <input type="hidden" id="created_at" name="created_at" value="">
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">View User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Username: <span id="viewUsername"></span></p>
                <p>User Type: <span id="viewUserType"></span></p>
                <p>Full Name: <span id="viewFullName"></span></p>
                <p>Email: <span id="viewEmail"></span></p>
                <p>Phone Number: <span id="viewPhoneNumber"></span></p>
                <p>Date of Birth: <span id="viewDOB"></span></p>
                <p>Created At: <span id="viewCreatedAt"></span></p>

            </div>
        </div>
    </div>
</div>
<!-- Edit User Modal -->

<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editUserForm">
                <div class="modal-body">
                    


                   
                    <div class="form-group">
                        <label for="editUsername">Username</label>
                        <input type="text" name="user_id" id="editUserId" hidden>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>

                    
                    <div class="form-group">
                        <label for="editPassword">Password</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                        <small class="form-text text-muted">Leave blank to keep the current password.</small>
                    </div>

                  
                    <div class="form-group">
                        <label for="editUserType">User Type</label>
                        <select class="form-control" id="editUserType" name="user_type">
                            <option value="admin">Admin</option>
                            <option value="technician">Technician</option>
                            <option value="receptionist">Receptionist</option>
                            <option value="patient">Patient</option>
                        </select>
                    </div>

            
                    <div class="form-group">
                        <label for="editFullName">Full Name</label>
                        <input type="text" class="form-control" id="editFullName" name="full_name" required>
                    </div>

                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>

                
                    <div class="form-group">
                        <label for="editPhoneNumber">Phone Number</label>
                        <input type="text" class="form-control" id="editPhoneNumber" name="phone_number">
                    </div>

                    <div class="form-group">
                        <label for="editDateOfBirth">Date of Birth</label>
                        <input type="date" class="form-control" id="editDateOfBirth" name="date_of_birth">
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

        $("#searchInput").on("keyup", function() {
            var searchText = $(this).val().toLowerCase();
            $("#userTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1)
            });
        });


        $("#addUserForm").submit(function(e) {
            e.preventDefault();


            var formData = $(this).serialize();


            $.post("addUser.php", formData, function(response) {

                console.log(response);

                if (response.success) {

                    alert("User added successfully!");


                    loadUserTable();


                    $("#addUserModal").modal("hide");
                    
                } else {

                    alert("Failed to add user. Please try again.");
                }
            }, "json");
        });


        function loadUserTable() {
            $.get("getUserTable.php", function(response) {

                $("#userTable tbody").html(response);
            });
        }
    });


    $(document).on('click', '.viewUserBtn', function() {
        var userId = $(this).data('user-id');

        $.ajax({
            url: 'getUserDetails.php',
            type: 'GET',
            data: {
                user_id: userId
            },
            dataType: 'json', 
            success: function(response) {
                $('#viewUsername').text(response.username || "N/A");
                $('#viewUserType').text(response.user_type || "N/A");
                $('#viewFullName').text(response.full_name || "N/A");
                $('#viewEmail').text(response.email || "N/A");
                $('#viewPhoneNumber').text(response.phone_number || "N/A");
                $('#viewDOB').text(response.date_of_birth || "N/A");
                $('#viewCreatedAt').text(response.created_at || "N/A");

                $('#viewUserModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error("Error fetching user details:", error);
            }
        });


    });

    $(document).on('click', '.editUserBtn', function() {
        var userId = $(this).data('user-id');
        $.ajax({
            url: 'getUserDetails.php',
            type: 'GET',
            data: {
                user_id: userId
            },
            dataType: 'json',
            success: function(response) {
                
                $('#editUserId').val(response.user_id); 
                $('#editUsername').val(response.username);
                $('#editUserType').val(response.user_type);
                $('#editFullName').val(response.full_name);
                $('#editEmail').val(response.email);
                $('#editPhoneNumber').val(response.phone_number);
                $('#editDateOfBirth').val(response.date_of_birth);

                $('#editUserModal').modal('show');
            }

        });
    });

    $('#editUserForm').submit(function(e) {
        e.preventDefault(); 


        var formData = $(this).serialize(); 
        console.log('Serialized form data:', formData); 

        $.ajax({
            url: 'updateUserDetails.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log('Success response:', response);
                
                var data = JSON.parse(response); 
                if (data.success) {
                    alert('User updated successfully.'); 
                    $('#editUserModal').modal('hide'); 
                    location.reload();
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            },

            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                // Error handling...
            }
        });



    });

    $(document).on('click', '.deleteUserBtn', function(e) {
        e.preventDefault(); 

        if (confirm('Are you sure you want to delete this user?')) {
            var userId = $(this).data('user-id');

            $.ajax({
                url: 'deleteUser.php',
                type: 'POST',
                data: {
                    user_id: userId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('User deleted successfully.');
                        
                        location.reload();
                    } else if (response.error) {
                        alert('Error: ' + response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert('An error occurred while deleting the user.');
                }
            });
        }
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>