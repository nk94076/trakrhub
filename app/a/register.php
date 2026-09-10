<?php
session_start();
include '../required/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hashing the password

    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        
        if ($stmt->execute()) {
            $_SESSION['user_email'] = $email;
            header("Location:   ../admin/dashboard/");
            exit();
        } else {
            $error = "Something went wrong!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <form method="POST" class="theme-form">
        <h4>Create an Account</h4>
        <p>Fill in the details below to register.</p>

        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <div class="form-group">
            <label>Full Name</label>
            <input class="form-control" type="text" name="name" required placeholder="Your Name">
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input class="form-control" type="email" name="email" required placeholder="Email">
        </div>

        <div class="form-group">
            <label>Password</label>
            <div class="form-input position-relative">
                <input class="form-control" type="password" name="password" required placeholder="*********">
                <div class="show-hide"><span class="show">👁️</span></div>
            </div>
        </div>

        <div class="text-end mt-3">
            <button class="btn btn-primary btn-block w-100" type="submit">Register</button>
        </div>
        
        <p class="mt-3">Already have an account? <a href="login.php">Login</a></p>
    </form>
</div>

<script>
    document.querySelector('.show-hide span').addEventListener('click', function () {
        let passwordField = document.querySelector('input[name="password"]');
        passwordField.type = passwordField.type === "password" ? "text" : "password";
    });
</script>
</body>
</html>
