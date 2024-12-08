<?php

ob_start();
session_start();
include_once 'config.php';

// Connect to server and select database using mysqli
$mysqli = new mysqli($host, $username, $password, $db_name);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Define $myusername, $mypassword, and $myemail from POST request
$myusername = $_POST['myusername'];
$mypassword = $_POST['mypassword'];
$myemail = $_POST['myemail'];

// To protect against MySQL injection
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myemail = stripslashes($myemail);

// Use prepared statements to prevent SQL injection
$myusername = $mysqli->real_escape_string($myusername);
$mypassword = $mysqli->real_escape_string($mypassword);
$myemail = $mysqli->real_escape_string($myemail);

// Hash the password using SHA1 and a salt
$mypassword = sha1($mypassword . $salt);

// Check if email already exists
$sql = "SELECT * FROM $tbl_name WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $myemail);
$stmt->execute();
$result = $stmt->get_result();
$count = $result->num_rows;

if ($count != 0) {
    echo "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Email ID already exists</div>";
} else {
    // Insert new user into the database
    $sql = "INSERT INTO $tbl_name (`id`, `username`, `password`, `email`) VALUES (NULL, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sss', $myusername, $mypassword, $myemail);
    $stmt->execute();

    // Set session variables
    $_SESSION['username'] = $myusername;
    $_SESSION['password'] = $mypassword;
    echo "true";
}

// Close the connection
$stmt->close();
$mysqli->close();

ob_end_flush();
?>
