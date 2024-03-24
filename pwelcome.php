<?php
session_start();
include('db_connection.php');




$user_id = $_SESSION['user_id'];


$upcomingAppointmentsQuery = "
select ap.appointment_date , apt.appoinment_type
from appoinment_type apt 
join appointments ap on apt.app_id = ap.app_id 
WHERE patient_id = $user_id AND ap.status='paid' AND ap.appointment_date > NOW() ORDER BY appointment_date ASC";
$upcomingAppointmentsResult = $conn->query($upcomingAppointmentsQuery);


$pastAppointmentsQuery = "
select ap.appointment_date , apt.appoinment_type
from appoinment_type apt 
join appointments ap on apt.app_id = ap.app_id  
WHERE patient_id = $user_id AND appointment_date <= NOW() ORDER BY appointment_date DESC";
$pastAppointmentsResult = $conn->query($pastAppointmentsQuery);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paitent Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding-top: 15px;
            color: #fff;
        }

        .sidebar-item {
            padding: 10px 20px;
            border-bottom: 1px solid #495057;
        }

        .sidebar-item a {
            color: #fff;
            text-decoration: none;
        }

        .sidebar-item a:hover {
            color: #fff;
            text-decoration: none;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .main-content h2 {
            margin-bottom: 20px;
        }

        .welcome-message {
            margin-bottom: 20px;
        }

        .logout-btn {
            margin-top: auto;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-item">
            <?php

            if (isset($_SESSION['user_type']) && isset($_SESSION['full_name'])) {
                echo "<a href='#'><i class='fas fa-user mr-2'></i>Welcome " . ucfirst($_SESSION['full_name']) . "</a>";
            }
            ?>
        </div>
        <div class="sidebar-item">
            <a href="pwelcome.php"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
        </div>
        <div class="sidebar-item">
            <a href="appoinment.php"><i class="fas fa-calendar-alt mr-2"></i>Appointment</a>
        </div>
        <div class="sidebar-item">
            <a href="#"><i class="fas fa-chart-bar mr-2"></i>Report</a>
        </div>
        <div class="sidebar-item logout-btn">
            <?php
            if (isset($_SESSION['user_type']) && isset($_SESSION['full_name'])) {
                echo "<a href='login.php'><i class='fas fa-sign-out-alt mr-2'></i>Logout</a>";
            }
            ?>
        </div>
    </div>

    <div class="main-content">
        <main>
            <section id="schedule-appointment">
                <h2>Schedule a New Appointment</h2>
                <form action="appointment_handler.php" method="post">
                    <input type="hidden" name="action" value="schedule">
                    <div class="form-group">
                        <label for="appointment_date">Appointment Date and Time:</label>
                        <input type="datetime-local" name="appointment_date" id="appointment_date" required class="form-control">
                    </div>

                    <div class="form-group">


                        <?php
                        $query = "SELECT * FROM appoinment_type Order by appoinment_type";
                        $result = $conn->query($query);

                        ?>
                        <select name="appointment_type" id="appointment_type" class="form-control" required>
                            <option value="">Appointment Type</option>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value=' . $row['app_id'] . '>' . $row['appoinment_type'] . '   ' . 'LKR' . $row['price'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                </form>
            </section>


            <section id="upcoming-appointments">
                <h2>Payement Summery</h2>

                <?php
                $query = "
                SELECT a.appointment_id, a.appointment_date, apt.*, a.status, u.full_name , a.remark
                FROM appointments a 
                JOIN users u ON a.patient_id = u.user_id
                JOIN appoinment_type apt on apt.app_id = a.app_id
                WHERE a.status != 'paid'
            ";

                if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'patient') {
                    $patientId = $_SESSION['user_id'];
                    $query .= " AND a.patient_id = " . $patientId; 
                } elseif (isset($_SESSION['user_type']) && ($_SESSION['user_type'] === 'admin' || $_SESSION['user_type'] === 'receptionist')) {
                    
                } else {
                    echo "Access denied.";
                    exit;
                }

                $query .= " ORDER BY a.appointment_date ASC";
                $result = $conn->query($query);



                $totalPriceQuery = "SELECT sum(price) AS total_price
                FROM appointments a 
                JOIN users u ON a.patient_id = u.user_id
                JOIN appoinment_type apt on apt.app_id = a.app_id
                where a.status ='confirm' and u.user_id='$user_id'";
                $totalPriceResult = $conn->query($totalPriceQuery);

                $totalPrice = 0;

                if ($totalPriceResult && $totalPriceResult->num_rows > 0) {
                    $row = $totalPriceResult->fetch_assoc();
                    $totalPrice = $row['total_price'];
                }


                ?>
                <ul>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAllAppointments" /></th> <!-- Select All Checkbox -->
                                <th>Date</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Remark</th>
                            </tr>
                        </thead>

                        <tbody id="appointmentTable">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                   
                                    if ($row['status'] === 'confirm') {
                                        echo "<td><input type='checkbox' class='selectAppointment' name='appointmentId[]' value='" . $row['appointment_id'] . "'></td>";
                                    } else {
                                        echo "<td></td>";
                                    }
                                    echo "<td>" . htmlspecialchars($row['appointment_date'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['appoinment_type'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['remark'], ENT_QUOTES, 'UTF-8') . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No appointments found.</td></tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" style="text-align: center;">
                                    <b>Total Price for Confirmed Appointments: LKR <?php echo $totalPrice; ?></b>
                                    <button id="checkoutButton" class="btn btn-primary">Checkout</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>

            </section>

            <!-- Checkout Modal -->
            <div class="modal" id="checkoutModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">User Details</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row mb-3">
                                    <label for="full_name" class="col-sm-4 col-form-label">Full Name:</label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="full_name">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="email" class="col-sm-4 col-form-label">Email:</label>
                                    <div class="col-sm-8">
                                        <input type="email" readonly class="form-control-plaintext" id="email">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="phone_number" class="col-sm-4 col-form-label">Phone Number:</label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="phone_number">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="date_of_birth" class="col-sm-4 col-form-label">Date of Birth:</label>
                                    <div class="col-sm-8">
                                        <input type="text" readonly class="form-control-plaintext" id="date_of_birth">
                                    </div>
                                </div>
                            </div>
                            <hr> <!-- Divider between user details and appointments -->
                            <div id="selectedAppointmentsDetails">
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">Selected Appointment Types:</div>
                                    <div class="col-sm-8" id="selectedAppointmentTypes"></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 font-weight-bold">Total Price:</div>
                                    <div class="col-sm-8" id="totalPrice"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="proceedToPayment" class="btn btn-primary">Proceed to Payment</button>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Payment Modal -->
            <div class="modal" id="paymentModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Payment Details</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <form id="paymentForm">
                                <!-- Payment Method Selection -->

                                <div class="form-group">
                                    <div class="form-check">

                                        <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="credit_card" checked>
                                        <label class="form-check-label" for="creditCard">
                                            Credit Card
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="paymentMethod" id="debitCard" value="debit_card">
                                        <label class="form-check-label" for="debitCard">
                                            Debit Card
                                        </label>
                                    </div>
                                </div>

                                <!-- Payment fields (simplified for demonstration) -->
                                <div class="form-group">
                                    <label for="cardNumber">Card Number:</label>
                                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" required>
                                </div>
                                <div class="form-group">
                                    <label for="amount">Amount:</label>
                                    <input type="text" class="form-control" id="amount" name="amount" readonly>
                                </div>
                                <!-- Add more fields -->
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button id="submitPayment" class="btn btn-success">Submit Payment</button>
                        </div>
                    </div>
                </div>
            </div>



            <section id="upcoming-appointments">
                <h2 class="mt-4 mb-3">Upcoming Appointments</h2>
                <div class="row">
                    <?php if ($upcomingAppointmentsResult->num_rows > 0) : ?>
                        <?php while ($appointment = $upcomingAppointmentsResult->fetch_assoc()) : ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($appointment['appoinment_type'], ENT_QUOTES, 'UTF-8') ?></h5>
                                        <p class="card-text">Date: <?= htmlspecialchars($appointment['appointment_date'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p>No upcoming appointments.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="past-appointments">
                <h2 class="mt-4 mb-3">Past Appointments</h2>
                <div class="row">
                    <?php if ($pastAppointmentsResult->num_rows > 0) : ?>
                        <?php while ($appointment = $pastAppointmentsResult->fetch_assoc()) : ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($appointment['appoinment_type'], ENT_QUOTES, 'UTF-8') ?></h5>
                                        <p class="card-text">Date: <?= htmlspecialchars($appointment['appointment_date'], ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p>No past appointments.</p>
                    <?php endif; ?>
                </div>
            </section>


        </main>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var selectAllCheckbox = document.getElementById('selectAllAppointments');
            selectAllCheckbox.addEventListener('change', function() {
                var allCheckboxes = document.querySelectorAll('.selectAppointment');
                for (var i = 0; i < allCheckboxes.length; i++) {
                    allCheckboxes[i].checked = this.checked;
                }
            });
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#checkoutButton').click(function() {
                var anyChecked = $('.selectAppointment:checked').length > 0;
                var userId = "<?php echo $_SESSION['user_id']; ?>";

                if (!anyChecked) {
                    alert('Please select at least one appointment for payment.');
                } else {
                    // Collecting appointment types and prices
                    var selectedTypes = [];
                    var totalPrice = 0;
                    $('.selectAppointment:checked').each(function() {
                        var row = $(this).closest('tr');
                        var type = row.find('td').eq(2).text();
                        var price = parseFloat(row.find('td').eq(3).text());
                        selectedTypes.push(type);
                        totalPrice += price;
                    });


                    $.ajax({
                        url: 'getUserDetails.php',
                        type: 'GET',
                        data: {
                            user_id: userId
                        },
                        success: function(data) {
                            console.log(data);
                            var userDetails = JSON.parse(data);

                            $('#full_name').val(userDetails.full_name);
                            $('#email').val(userDetails.email);
                            $('#phone_number').val(userDetails.phone_number);
                            $('#date_of_birth').val(userDetails.date_of_birth);

                            // Updating the modal with appointment details
                            $('#selectedAppointmentTypes').text(selectedTypes.join(', '));
                            $('#totalPrice').text('LKR ' + totalPrice.toFixed(2));

                            $('#checkoutModal').modal('show');
                        }
                    });
                }
            });

            function getTotalPrice() {
                var totalPrice = 0;
                $('.selectAppointment:checked').each(function() {
                    var row = $(this).closest('tr');
                    var price = parseFloat(row.find('td').eq(3).text());
                    totalPrice += price;
                });
                return totalPrice.toFixed(2);
            }


            $('#proceedToPayment').click(function() {
                $('#checkoutModal').modal('hide');
                $('#paymentModal').modal('show');
                $('#amount').val(getTotalPrice());
            });

            $('#submitPayment').click(function(e) {
                e.preventDefault();

                var patient_id = "<?php echo $_SESSION['user_id']; ?>";

               
                var paymentData = {
                    patient_id: patient_id,
                    amount: $('#amount').val(),
                    payment_method: $('input[name="paymentMethod"]:checked').val() == 'creditCard' ? 'credit_card' : 'debit_card',
                    payment_status: 'pending',
                };

                console.log(paymentData);

                // AJAX request
                $.ajax({
                    type: "POST",
                    url: "save_payment.php",
                    data: paymentData,
                    success: function(response) {
                        
                        var selectedAppointmentIds = $('.selectAppointment:checked').map(function() {
                            return this.value;
                        }).get();

                        if (selectedAppointmentIds.length > 0) {
                            $.ajax({
                                type: "POST",
                                url: "update_appointments_status.php",
                                data: {
                                    appointmentIds: selectedAppointmentIds
                                },
                                success: function(updateResponse) {
                                    alert('Payment Success');
                                    $('#paymentModal').modal('hide');
                                    location.reload();
                                },
                                error: function() {
                                    alert('An error occurred while updating appointments.');
                                }
                            });
                        }
                    },
                    error: function() {
                        alert('An error occurred. Please try again.');
                    }
                });
            });

        });
    </script>
</body>

</html>