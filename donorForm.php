<?php
include "connection.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
if(isset($_POST["submit"]) )
{
	// Form data
	$name = $_POST['name'];
	$email = $_POST['email'];
	$aadhaar = $_POST['aadhaar'];
	$contact = $_POST['contact'];
	$password = $_POST['password'];
	$address = $_POST['address'];
	$pincode = $_POST['pincode'];
	$username = $_POST['username'];
	
	
}

// SQL to insert data into database
$sql = "INSERT INTO donors (name, email, aadhaar, contact, password, address, pincode, username)
        VALUES ('$name', '$email', '$aadhaar', '$contact', '$password', '$address', '$pincode', '$username')";

if ($conn->query($sql) === TRUE) {
    echo "Registered successfully.You can now login to continue.";
	echo "<a href= 'donorLogin.html'>Login</a>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
