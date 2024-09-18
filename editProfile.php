<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	include "connection.php";
    if(isset($_POST["submit"]) )
	{	
		$NewUsername = $_POST['NewUsername'];
		$password = $_POST['password'];
		$donor_id = $_POST['donor_id'];
		$sql = "SELECT * FROM donors WHERE username = '$NewUsername' and donor_id != '$donor_id'";
			
		$result = $conn->query($sql);
		$num_rows = mysqli_num_rows($result);
		if($num_rows != 0)
		{
			echo "<script>alert('username already used by someone'); window.location.href = 'donorD.php';</script>";
			exit;
					
		}
		else
		{
			$sql_update = "UPDATE donors SET username='$NewUsername',password='$password' WHERE donor_id ='$donor_id'";
				if ($conn->query($sql_update) == TRUE)
				{
					
					session_start(); 
					$_SESSION['username'] = $NewUsername; // Update the session data
					$_SESSION['password'] = $password;
					
					session_write_close();
				  	echo "<script>alert('updated successfully'); window.location.href = 'donorD.php';</script>";
        exit;
				 
				}
				else 
				{
  					echo "<script>alert('Error'); window.location.href = 'donorD.php';</script>";
        exit;
				}
		}
	}
