<?php
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			include "connection.php";

			if(isset($_POST["submit"]) )
			{
			
				$robinId = $_POST['robinId'];
				$donation_id = $_POST['donation_id'];
				
				$sql = "UPDATE donations SET robinId='$robinId' WHERE donation_id ='$donation_id'";

				if ($conn->query($sql) === TRUE)
				{
				  echo "<script>alert('Record updated successfully'); window.location.href = 'poc_Dashboard.php';</script>";
        exit;
				 
				}
				else 
				{
  						echo "Error updating record: " . $conn->error;
				}
				
				exit;
				
			}
?>
