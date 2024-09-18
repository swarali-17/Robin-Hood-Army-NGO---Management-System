<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>poc_dashboard</title>
    <link rel="stylesheet" type="text/css" href="poc_dashboard.css"> 
    
</head>
<body>
	<main>
	<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include "connection.php";
	if(isset($_POST["submit"]) )
	{
		$username = $_POST['username'];
		$name = $_POST['name'];
		$contact = $_POST['contact'];
		$email = $_POST['email'];
		$aadhaar = $_POST['aadhaar'];
		$pincode = $_POST['pincode'];
		$address = $_POST['address'];
		$password = $_POST['password'];
		
		
	}
	//echo "$username";
	$sql1 = "SELECT * FROM pocs WHERE username = '$username'";
	$result = $conn->query($sql1);
	$num_rows = mysqli_num_rows($result);
	
	$sql2 = "SELECT * FROM pocs WHERE pincode = '$pincode'";
	$result2 = $conn->query($sql2);
	$num_rows2 = mysqli_num_rows($result2);
	if($num_rows != 0) 
		echo "username already exists";
	else if($num_rows2 != 0)
	{
		echo" sorry, poc already exists at that pincode. return to";
		echo "<a href= 'index.html'>  Homepage</a>";
	}
	else
	{
		// SQL to insert data into database
		$sql = "INSERT INTO pocs( username, name, password, contact,pincode, address , email , aadhaar)
		    VALUES ('$username', '$name', '$password','$contact','$pincode', '$address', '$email', '$aadhaar')";

		if ($conn->query($sql) === TRUE)
		{
			echo "<h3> Registered Successfully ! Please login now to continue</h3><br><br><br>";
			echo "<a href= 'poc_Login.php'>Login</a>";
			
		}
		else
		{
			echo "Error: " . $sql . "<br>" . $connection->error;
		}
	}

	$conn->close();
	?>
</body>
</html>