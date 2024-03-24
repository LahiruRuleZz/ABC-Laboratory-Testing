<?php
include_once "db_connection.php";

$email = $password = "";
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($email)) {
        array_push($errors, "Email is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }

    if (count($errors) == 0) {
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);


            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];


                if ($user['user_type'] == 'patient') {
                    header('location: pwelcome.php');
                } 
                elseif ($user['user_type'] == 'admin') {
                    header('location: awelcome.php');
                }
                elseif ($user['user_type'] == 'technician') {
                    header('location: twelcome.php');
                }
                elseif ($user['user_type'] == 'receptionist') {
                    header('location: rwelcome.php');
                }
                
                else {

                    echo "Invalid user type";
                }
                exit();
            } else {
                array_push($errors, "Invalid email/password combination");
            }
        } else {
            array_push($errors, "User not found");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-login {
            width: 100%;
        }

        .register-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2 class="card-title">User Login</h2>
        <form method="post" action="login.php">
            <?php include('errors.php'); ?>
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary btn-login" name="login">Login</button>
            <a href="registration.php" class="register-link">Not a member? Register here</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>