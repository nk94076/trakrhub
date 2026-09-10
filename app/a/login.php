<?php
ob_start();
session_start();
if (isset($_SESSION['user_id'])) {
    // Redirect to dashboard if already logged in
    header("Location: https://app.trakrhub.com/a/redirect-user.php");
    exit();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../required/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($remember) {
            setcookie("user_email", $user['email'], time() + (30 * 24 * 60 * 60), "/");
        }

       // Fetch actual redirect page from PHP
header("Location: https://app.trakrhub.com/a/redirect-user.php");
exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Adtrackr ">
    <meta name="keywords" content="admin template, Adtrackr">
    <meta name="author" content="Naveen Kumar">
    <link rel="icon" href="../assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="../assets/images/favicon.png" type="image/x-icon">
    <title>Login  | Adtrackr </title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="../assets/css/fontawesome.css">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/icofont.css">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/themify.css">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/flag-icon.css">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/feather-icon.css">
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="../assets/css/vendors/bootstrap.css">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
    <link id="color" rel="stylesheet" href="../assets/css/color-1.css" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="../assets/css/responsive.css">
  </head>
  <style>

  </style>
  <body>
    <!-- login page start-->
    <div class="container-fluid">
      <div class="row">
          
           <div class="col-xl-6 p-0">
          <div class="login-card login-dark" style="background:#303f8e;">
            <div>
              <div><a class="logo text-center" href="login.php"><img class="img-fluid for-light" src="../assets/images/logo/logo_dark.png" alt="looginpage" width="250px"><img class="img-fluid for-dark" src="../assets/images/logo/logo.png" alt="looginpage"></a></div>
              <div class="login-main"> 
               <form method="POST" class="theme-form">
                    <h4>Sign in to account</h4>
                    <p>Enter your email & password to login</p>
                    
                    <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            
                    <div class="form-group">
                        <label>Email Address</label>
                        <input class="form-control" type="email" name="email" required placeholder="Email">
                    </div>
            
                    <div class="form-group">
                        <label>Password</label>
                        <div class="form-input position-relative">
                            <input class="form-control" type="password" name="password" required placeholder="*********">
                            <div class="show-hide"><span class="show">️</span></div>
                        </div>
                    </div>
            
                    <div class="form-group mb-0">
                        <div class="checkbox p-0">
                            <input id="remember" name="remember" type="checkbox">
                            <label for="remember">Remember password</label>
                        </div>
                        <a class="link" href="forgot-password.php">Forgot password?</a>
                        <div class="text-end mt-3">
                            <button class="btn btn-primary btn-block w-100" type="submit">Sign in</button>
                        </div>
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
          
        <div class="col-xl-6"><img class="bg-img-cover bg-center" src="../assets/images/login/1.png" width="50%" alt="looginpage"></div>
       
      </div>
      <!-- latest jquery-->
      <script src="../assets/js/jquery.min.js"></script>
      <!-- Bootstrap js-->
      <script src="../assets/js/bootstrap/bootstrap.bundle.min.js"></script>
      <!-- feather icon js-->
      <script src="../assets/js/icons/feather-icon/feather.min.js"></script>
      <script src="../assets/js/icons/feather-icon/feather-icon.js"></script>
      <!-- scrollbar js-->
      <!-- Sidebar jquery-->
      <script src="../assets/js/config.js"></script>
      <!-- Plugins JS start-->
      <!-- Plugins JS Ends-->
      <!-- Theme js-->
      <script src="../assets/js/script.js"></script>
      <script src="../assets/js/script1.js"></script>
    </div>
  </body>
  <script>
    document.querySelector('.show-hide span').addEventListener('click', function () {
        let passwordField = document.querySelector('input[name="password"]');
        passwordField.type = passwordField.type === "password" ? "text" : "password";
    });
</script>
</html>