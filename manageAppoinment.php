<?php
include_once "commonfiles/header.php";
include('db_connection.php');


$query = "SELECT *
          FROM appoinment_type";

// Modify query based on user role
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'patient') {
    // For patients, only show their own appointments
    $patientId = $_SESSION['user_id'];
    $query .= " WHERE a.patient_id = " . $patientId;
} elseif (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'receptionist')) {
} else {
    // Handle not logged in or other roles
    echo "Access denied.";
    exit;
}

$query .= " ORDER BY appoinment_type ASC";
$result = $conn->query($query);

?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Appoinment Type Management</h2>
        <div class="form-inline">
            <div class="input-group mr-3">

                <!-- Search Bar -->
                <div class="search-bar">
                    <input type="text" class="form-control" id="searchInput" onkeyup="searchAppointments()" placeholder="Search for appointments...">
                </div>
            </div>
            <button id="addUserBtn" class="btn btn-success" data-toggle="modal" data-target="#addUserModal"><i class="fas fa-plus"></i> Add Appointment Type</button>
        </div>
    </div>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Appointment Type</th>
                <th>Price</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody id="appointmentTable">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['appoinment_type'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td class='text-center'>";
                    // Centering action buttons with spacing and rounded corners
                    echo "<div class='btn-group' role='group'>";
                    if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'receptionist')) {
                        echo "<a href='#' class='btn btn-sm btn-primary editUserBtn' data-app-id='" . $row['app_id'] . "'><i class='fas fa-edit'></i> Edit</a>";
                    } else {
                        echo "N/A";
                    }
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No appointments found.</td></tr>";
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
                <h5 class="modal-title" id="addUserModalLabel">Add Appointment Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="form-group">
                        <label for="appointment_type">Appointment Type</label>
                        <input type="text" class="form-control" id="appointment_typeid" name="appointment_type" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Edit Appointment Modal -->
<div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAppointmentModalLabel">Edit Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAppointmentForm">
                    <div class="form-group">
                        <label for="editAppointmentDate">Appointment Type:</label>
                        <input type="text" id="editAppointmentType" name="appointmenttype" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editAppointmentType">Price:</label>
                        <input type="text" id="editAppointmentprice" name="appointmentprice" class="form-control" required>
                    </div>
                    <input type="hidden" id="editAppointmentid" name="appointmentId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitEditAppointment()">Save Changes</button>

            </div>
        </div>
    </div>
</div>




<?php include_once "commonfiles/script.php"; ?>

<!-- Bootstrap and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Search Function -->
<script>
    function searchAppointments() {
        var input, filter, table, tr, i, txtValue, found;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("appointmentTable");
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td");
            found = false;
            for (let j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            if (found) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }

    $("#addUserForm").submit(function(e) {
        e.preventDefault();


        var formData = $(this).serialize();


        $.post("addAppoinment.php", formData, function(response) {

            console.log(response);

            if (response.success) {

                alert("Appointment type added successfully!");





                $("#addUserModal").modal("hide");
                location.reload();

            } else {

                alert("Failed to Appointment type. Please try again.");
            }
        }, "json");
    });


    $(document).on('click', '.editUserBtn', function() {
        var app_id = $(this).data('app-id');

        $.ajax({
            url: 'getAppointmenttype.php',
            type: 'GET',
            data: {
                app_id: app_id
            },
            dataType: 'json',
            success: function(response) {

                $('#editAppointmentType').val(response.appointment_type);
                $('#editAppointmentprice').val(response.price);
                $('#editAppointmentid').val(response.app_id);

                $('#editAppointmentModal').modal('show');
            }

        });
    });


    function submitEditAppointment() {
        // Prevent default form submission
        event.preventDefault();

        // Serialize form data
        var formData = $('#editAppointmentForm').serialize();
        console.log('Serialized form data:', formData);

        // Perform AJAX request
        $.ajax({
            url: 'updateAppoinmentType.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log('Success response:', response);
                var data = JSON.parse(response);
                if (data.success) {
                    alert('Appointment updated successfully.');
                    $('#editAppointmentModal').modal('hide'); // Note: Changed from editUserModal to editAppointmentModal
                    location.reload();
                } else if (data.error) {
                    alert('Error: ' + data.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                // Additional error handling...
            }
        });
    }


    // $('#editAppointmentForm').submit(function(e) {
    //     e.preventDefault();


    //     var formData = $(this).serialize();
    //     console.log('Serialized form data:', formData);

    //     $.ajax({
    //         url: 'updateAppoinmentType.php',
    //         type: 'POST',
    //         data: formData,
    //         success: function(response) {
    //             console.log('Success response:', response);

    //             var data = JSON.parse(response);
    //             if (data.success) {
    //                 alert('User updated successfully.');
    //                 $('#editUserModal').modal('hide');
    //                 location.reload();
    //             } else if (data.error) {
    //                 alert('Error: ' + data.error);
    //             }
    //         },

    //         error: function(xhr, status, error) {
    //             console.error('AJAX error:', error);
    //             // Error handling...
    //         }
    //     });



    //});
</script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->

<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Include Popper.js (required for Bootstrap 4) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>