<?php

ob_start();
session_start();
include_once 'config.php';

// Connect to server and select database using mysqli
$mysqli = new mysqli($host, $username, $password, $db_name);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$value = isset($_POST['myemail']) ? $_POST['myemail'] : '';
$myemail = $mysqli->real_escape_string($value);
$mypassword = isset($_POST['mypassword']) ? $_POST['mypassword'] : '';

// Protect against SQL injection by escaping user input
$myemail = stripslashes($myemail);
$mypassword = stripslashes($mypassword);

// Hash the password using SHA1 and a salt
$mypassword = sha1($mypassword . $salt);

// Prepare and execute the SQL query
$sql = "SELECT * FROM $tbl_name WHERE email = ? AND password = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ss', $myemail, $mypassword);
$stmt->execute();
$result = $stmt->get_result();

// Check if the query returned exactly one row
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    // Start the session and set session variables
    $_SESSION['username'] = $row['username'];
    $_SESSION['password'] = $mypassword;
    
    echo "true"; // Successful login
} else {
    // Return an error message
    echo "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Wrong Username or Password</div>";
}

// Close the prepared statement and connection
$stmt->close();
$mysqli->close();

ob_end_flush();
?>
