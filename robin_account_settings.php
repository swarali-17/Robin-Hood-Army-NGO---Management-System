<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include "connection.php";
    if(isset($_POST["submit"]) )
	{	
		$NewUsername = $_POST['NewUsername'];
		$password = $_POST['password'];
		$robinId = $_POST['robinId'];
		$name = $_POST['name'];
		$contactNumber = $_POST['contactNumber'];
		$residential_address = $_POST['residential_address'];
		$sql = "SELECT * FROM robins WHERE username = '$NewUsername' and robinId != '$robinId'";
			
		$result = $conn->query($sql);
		$num_rows = mysqli_num_rows($result);
		if($num_rows != 0)
		{
			echo "<script>alert('username already used by someone'); window.location.href = 'robin_Dashboard.php';</script>";
			exit;
					
		}
		else
		{
			$sql_update = "UPDATE robins SET username='$NewUsername',password='$password',name = '$name',contactNumber = '$contactNumber',residential_address='$residential_address' WHERE robinId ='$robinId'";
				if ($conn->query($sql_update) == TRUE)
				{
					
					session_start(); 
					$_SESSION['username'] = $NewUsername; // Update the session data
					$_SESSION['password'] = $password;
					
					session_write_close();
				  	echo "<script>alert('updated successfully'); window.location.href = 'robin_Dashboard.php';</script>";
        exit;
				 
				}
				else 
				{
  					echo "<script>alert('Error'); window.location.href = 'robin_Dashboard.php';</script>";
        exit;
				}
		}
	}
