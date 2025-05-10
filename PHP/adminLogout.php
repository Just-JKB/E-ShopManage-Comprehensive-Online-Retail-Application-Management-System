<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to admin login page
header("Location: ../HTML/adminLogin.html");
exit();
