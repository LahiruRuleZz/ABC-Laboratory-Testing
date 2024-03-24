<?php
include_once "commonfiles/header.php";
include('db_connection.php');


$query = "SELECT a.appointment_id, a.appointment_date, apt.*, a.status, u.full_name 
FROM appointments a 
JOIN users u ON a.patient_id = u.user_id
JOIN appoinment_type apt on apt.app_id = a.app_id";

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

$query .= " ORDER BY a.appointment_date ASC";
$result = $conn->query($query);

?>


<div class="container mt-5">
    <h2>Manage Appointments</h2>
    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" class="form-control" id="searchInput" onkeyup="searchAppointments()" placeholder="Search for appointments...">
    </div>
    <br>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Date</th>
                <th>Type</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="appointmentTable">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['appointment_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['appoinment_type'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>";


                    if (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'receptionist')) {
                        echo "<button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editAppointmentModal' data-appointment-id='" . $row['appointment_id'] . "'>Edit</button>
                          <button class='btn btn-danger btn-sm' onclick='cancelAppointment(" . $row['appointment_id'] . ")'>Cancel</button>
                          <button class='btn btn-success btn-sm' data-toggle='modal' data-target='#rescheduleModal' data-appointment-id='" . $row['appointment_id'] . "'>Reschedule</button>
                          <button class='btn btn-info btn-sm' onclick='confirmAppointment(" . $row['appointment_id'] . ")'>Confirm</button>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No appointments found.</td></tr>";
            }
            ?>
        </tbody>
    </table>


</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Appointment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rescheduleForm">
                    <div class="form-group">
                        <label for="newAppointmentDate">New Date and Time:</label>
                        <input type="datetime-local" id="newAppointmentDate" name="newAppointmentDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="rescheduleRemark">Remark:</label>
                        <textarea id="rescheduleRemark" name="remark" class="form-control" rows="3" required></textarea>
                    </div>
                    <input type="hidden" id="appointmentIdToReschedule" name="appointmentId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="submitReschedule()">Save changes</button>
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
                        <label for="editAppointmentDate">Date and Time:</label>
                        <input type="datetime-local" id="editAppointmentDate" name="appointmentDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editAppointmentType">Appointment Type:</label>
                        <input type="text" id="editAppointmentType" name="appointmentType" class="form-control" required>
                    </div>
                    <input type="hidden" id="editAppointmentId" name="appointmentId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitEditAppointment()">Save Changes</button>
            </div>
        </div>
    </div>
</div>



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

    function cancelAppointment(appointmentId) {
        if (confirm('Are you sure you want to cancel this appointment?')) {
            $.ajax({
                url: 'cancel_appointment.php',
                type: 'POST',
                data: {
                    appointment_id: appointmentId
                },
                success: function(response) {

                    alert(response);

                    location.reload();
                },
                error: function() {
                    alert('Error cancelling appointment.');
                }
            });
        }
    }

    function confirmAppointment(appointmentId) {

        $.ajax({
            url: 'confirm_appointment.php',
            type: 'POST',
            data: {
                appointment_id: appointmentId
            },
            success: function(response) {

                alert(response);

                location.reload();
            },
            error: function() {
                alert('Error cancelling appointment.');
            }
        });

    }
    $('#rescheduleModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var appointmentId = button.data('appointment-id');
        var modal = $(this);
        modal.find('.modal-body #appointmentIdToReschedule').val(appointmentId);
    });

    function submitReschedule() {
        var appointmentId = $('#appointmentIdToReschedule').val();
        var newDate = $('#newAppointmentDate').val();
        var remark = $('#rescheduleRemark').val(); 

        $.ajax({
            url: 'reschedule_appointment.php',
            type: 'POST',
            data: {
                appointment_id: appointmentId,
                new_appointment_date: newDate,
                remark: remark
            },
            success: function(response) {
                alert(response);
                $('#rescheduleModal').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                alert('Error rescheduling appointment.');
            }
        });
    }



    $('#rescheduleModal').on('hidden.bs.modal', function() {
        location.reload();
    });


    $('#editAppointmentModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var appointmentId = button.data('appointment-id');
        var modal = $(this);
        modal.find('#editAppointmentId').val(appointmentId);
    });

    function submitEditAppointment() {
        var formData = $('#editAppointmentForm').serialize();
        console.log(formData);

        $.ajax({
            url: 'update_appointment.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                alert(response);
                $('#editAppointmentModal').modal('hide');
                location.reload();
            },
            error: function() {
                alert('Error updating appointment.');
            }
        });
    }

    $('#editAppointmentModal').on('hidden.bs.modal', function() {
        location.reload();
    });
</script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->

<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Include Popper.js (required for Bootstrap 4) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>

</html>