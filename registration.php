<?php
include_once "db_connection.php";

$name = $email = $phone = $dob = $password = "";
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($name)) { array_push($errors, "Name is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($phone)) { array_push($errors, "Phone number is required"); }
    if (empty($dob)) { array_push($errors, "Date of birth is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }

    if (count($errors) == 0) {
        // Encrypt password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (full_name, email, phone_number, date_of_birth, password, user_type)
                  VALUES('$name', '$email', '$phone', '$dob', '$hashed_password', 'patient')";
        mysqli_query($conn, $query);
        header('location: login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label, input, button {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Registration</h2>
        <form method="post" action="registration.php">
            <?php include('errors.php'); ?>
            <label>Name</label>
            <input type="text" name="name" value="<?php echo $name; ?>">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $email; ?>">
            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo $phone; ?>">
            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?php echo $dob; ?>">
            <label>Password</label>
            <input type="password" name="password">
            <button type="submit" name="register">Register</button>
        </form>
    </div>
</body>
</html>
