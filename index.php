<?php
// Redirect to the login page. The domain is fixed (not taken from the
// client-supplied Host header) to prevent it being used as an open redirect.
header("Location: https://app.trakrhub.com/a/login.php");
exit();
?>
