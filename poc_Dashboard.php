<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>poc_dashboard</title>
    <link rel="stylesheet" type="text/css" href="poc_dashboard.css"> 
</head>
<body>
    <header>
        <h3>Robinhood Point of Contact </h3>

        <div class="tab">
		  	<button class="tablinks" onclick="openTab(event, 'Home')" id="defaultOpen">Home</button>
		  	<button class="tablinks" onclick="openTab(event, 'Unassigned Donations')">Unassigned Donations</button>
		  	<button class="tablinks" onclick="openTab(event, 'Assigned Donations')">Assigned Donations</button>
		  	<button class="tablinks" onclick="openTab(event, 'Account Settings')" >Account Settings</button>
		  	<button class="tablinks" onclick="openTab(event, 'Analytics')" >Analytics</button>
			<button onclick="logout()">Logout</button>
		</div>
	</header>
		
	<div id="Home" class="tabcontent">
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include "connection.php";
    
    session_start();
    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    $sql = "SELECT * FROM pocs WHERE username = '$username'";
    $result = $conn->query($sql);
    
    // Retrieve the details of the POC
    $row = $result->fetch_assoc();
    $pincode = $row['pincode'];
    $address = $row['address'];
    $poc_id = $row['poc_id'];
    $name = $row['name'];
    $email = $row['email'];
    $contact = $row['contact'];
    ?>
    <div class="poc-details">
        <h4>POC Details:</h4>
        <ul>
            <li><strong>POC ID:</strong> <?php echo $poc_id; ?></li>
            <li><strong>Branch:</strong> <?php echo $address; ?></li>
            <li><strong>Pincode:</strong> <?php echo $pincode; ?></li>
            <li><strong>Name of Head:</strong> <?php echo $name; ?></li>
            <li><strong>Email:</strong> <?php echo $email; ?></li>
            <li><strong>Contact:</strong> <?php echo $contact; ?></li>
        </ul>
    </div>
    <hr>

    <?php
    // Retrieve the Robin details under that POC
    $sql_robinDetails = "SELECT * FROM robins WHERE poc_id = '$poc_id' ";
    $result_robinDetails = $conn->query($sql_robinDetails);
    ?>
    <div class="robins-list">
        <h4>Robins working here:</h4>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Residential Address</th>
            </tr>
            <?php
            while ($row_robinDetails = $result_robinDetails->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo $row_robinDetails['robinId']; ?></td>
                    <td><?php echo $row_robinDetails['name']; ?></td>
                    <td><?php echo $row_robinDetails['contactNumber']; ?></td>
                    <td><?php echo $row_robinDetails['email']; ?></td>
                    <td><?php echo $row_robinDetails['residential_address']; ?></td>
                </tr>
            <?php }
			 ?>
        </table>
    </div>
</div>


<div id="Unassigned Donations" class="tabcontent">
    <h5>These donations have not been assigned a Robin:</h5>
    <table>
        <tr>
            <th>Donation ID</th>
            <th>Donor ID</th>
            <th>Donor Name</th>
            <th>Donor Contact</th>
            <th>Assign Robin</th>
        </tr>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Your SQL query to fetch unassigned donations
        $sql_donationDetails = "SELECT x.donor_id, x.name, x.contact, y.donation_id
                                FROM donors x, donations y 
                                WHERE x.donor_id = y.donor_id 
                                      AND x.pincode = '$pincode' 
                                      AND y.robinId IS NULL";
        $result_donationDetails = $conn->query($sql_donationDetails);
        if ($result_donationDetails->num_rows > 0) {
            while ($row_donationDetails = $result_donationDetails->fetch_assoc()) {
                $donation_id = $row_donationDetails['donation_id'];
                $donor_id = $row_donationDetails['donor_id'];
                $name = $row_donationDetails['name'];
                $contact = $row_donationDetails['contact'];

                echo "<tr>";
                echo "<td>$donation_id</td>";
                echo "<td>$donor_id</td>";
                echo "<td>$name</td>";
                echo "<td>$contact</td>";
                echo "<td>";
                echo "<form method='POST' action='assign_robin.php'>";
                echo "<label for='robinId'></label>";
                echo "<select name='robinId' id='robinId'>";
                echo "<option value=''>-- Select the robinId --</option>";
                
                $arr = array();
                // Your SQL query to fetch relevant robinIds based on the pincode
                $sql_robinIds = "SELECT robinId FROM robins WHERE pincode = '$pincode'";
                $result_robinIds = $conn->query($sql_robinIds);

                // Fetch and populate $arr with relevant robinIds
                while ($row_robinIds = $result_robinIds->fetch_assoc()) {
                    $arr[] = $row_robinIds['robinId'];
                }  
                // Populate the dropdown with robinIds
                foreach ($arr as $robinId) {
                    echo "<option value='$robinId'>$robinId</option>";
                }
                
                echo "</select>";
                echo "<input type='hidden' name='donation_id' value='$donation_id'>";
                echo "<input type='submit' value='Assign' name='submit'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No unassigned donations found.</td></tr>";
        }
        ?>
    </table>
</div>



<div id="Assigned Donations" class="tabcontent">
    <h5>Check donation details:</h5>
    <table>
        <tr>
            <th>Donation ID</th>
            <th>Robin ID</th>
            <th>Donor ID</th>
            <th>Donor Name</th>
            <th>Donor Contact</th>
            <th>Status</th>
        </tr>
        <?php
        // Your SQL query to fetch assigned donations
        $sql_donationDetails2 = "SELECT x.donor_id, x.name, x.contact, y.donation_id, y.robinId, y.status 
                                FROM donors x, donations y 
                                WHERE x.donor_id = y.donor_id 
                                      AND x.pincode = '$pincode' 
                                      AND robinId IS NOT NULL";
        $result_donationDetails2 = $conn->query($sql_donationDetails2);
        while ($row_donationDetails2 = $result_donationDetails2->fetch_array()) {
            $donation_id = $row_donationDetails2['donation_id'];
            $donor_id = $row_donationDetails2['donor_id'];
            $name = $row_donationDetails2['name'];
            $contact = $row_donationDetails2['contact'];
            $robinId = $row_donationDetails2['robinId'];
            $status = $row_donationDetails2['status'];

            echo "<tr>";
            echo "<td>$donation_id</td>";
            echo "<td>$robinId</td>";
            echo "<td>$donor_id</td>";
            echo "<td>$name</td>";
            echo "<td>$contact</td>";
            echo "<td>";
            if ($status == 0) {
                echo "<p style='color:red;'>Pending</p>";
            } else {
                echo "<p style='color:green;'>Completed</p>";
            }
            echo "</td>";
            echo "</tr>";
        }

        // Additional SQL query if needed
        $sql_donationDetails3 = "SELECT x.donor_id, x.name, x.contact, y.donation_id, y.robinId, y.status 
                                FROM donors_backup x, donations_backup y 
                                WHERE x.donor_id = y.donor_id 
                                      AND x.pincode = '$pincode' 
                                      AND robinId IS NOT NULL";
        $result_donationDetails3 = $conn->query($sql_donationDetails3);
        while ($row_donationDetails3 = $result_donationDetails3->fetch_array()) {
            $donation_id_backup = $row_donationDetails3['donation_id'];
            $donor_id_backup = $row_donationDetails3['donor_id'];
            $name_backup = $row_donationDetails3['name'];
            $contact_backup = $row_donationDetails3['contact'];
            $robinId_backup = $row_donationDetails3['robinId'];
            $status_backup = $row_donationDetails3['status'];

            echo "<tr>";
            echo "<td>$donation_id_backup</td>";
            echo "<td>$robinId_backup</td>";
            echo "<td>$donor_id_backup</td>";
            echo "<td>$name_backup</td>";
            echo "<td>$contact_backup</td>";
            echo "<td>";
            if ($status_backup == 0) {
                echo "<p style='color:red;'>Pending</p>";
            } else {
                echo "<p style='color:green;'>Completed</p>";
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

		
		<div id="Analytics" class="tabcontent">
			<?php
 				error_reporting(E_ALL);
		  		ini_set('display_errors', 1);
		
				$dataPoints = array();

				$arr = array(); // Initialize the array to store Robin IDs

// Retrieve the Robin details under that POC
$sql_robinDetails = "SELECT robinId FROM robins WHERE poc_id = '$poc_id' ";
$result_robinDetails = $conn->query($sql_robinDetails);

// Loop through the results and store Robin IDs in the array
while ($row_robinDetails = $result_robinDetails->fetch_assoc()) {
    $arr[] = $row_robinDetails['robinId'];
}

					foreach ($arr as $x)
					{
  						$sql_don_robin = "SELECT 
            (SELECT COUNT(robinId) FROM donations WHERE robinId = '$x') AS count1,
            (SELECT COUNT(robinId) FROM donations_backup WHERE robinId = '$x') AS count2
        FROM dual";
  						
  						$result_don_robin = $conn->query($sql_don_robin);
  						$row_don_robin = mysqli_fetch_assoc($result_don_robin);
  						$number = $row_don_robin['count1'] + $row_don_robin['count2'];
  						$dataPoints[] = array("y" => $number, "label" => $x);
					}
					/*array("y" => 3373.64, "label" => "Germany" ),
					array("y" => 2435.94, "label" => "France" ),
					array("y" => 1842.55, "label" => "China" ),
					array("y" => 1828.55, "label" => "Russia" ),
					array("y" => 1039.99, "label" => "Switzerland" ),
					array("y" => 765.215, "label" => "Japan" ),
					array("y" => 612.453, "label" => "Netherlands" )*/
			
			?>
			<script>
				window.onload = function() {
				 
				var chart = new CanvasJS.Chart("chartContainer", {
					animationEnabled: true,
					theme: "light2",
					title:{
						text: "Donations made by Robins"
					},
					axisY: {
						title: "No of donations",
						interval: 1
					},
					axisX: {
						title: "Robin ID",
						
					},
					data: [{
						type: "column",
						yValueFormatString: "#,##0.## donations",
						dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
					}]
				});
				chart.render();
				 
				}
			</script>
 			<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
			
		</div>
		
		
		<div id="Account Settings" class="tabcontent mini-box">
			<p style="text-align: center;"><strong> Edit the fields which you want to update </strong></p>
			
            <form  method="POST"action='poc_account_settings.php'>
                
			<p style="text-align: center;"><label for="NewUsername">username  </label></p>
			<p style="text-align: center;">   <input type="text" id="NewUsername" name="NewUsername" value="<?php echo"$username"; ?>"><br></p>

			<p style="text-align: center;">  <label for="password">password  </label></p>
			<p style="text-align: center;">  <input type="text" id="password" name="password" value="<?php echo"$password"; ?>"><br></p>
                
			<p style="text-align: center;"><label for="contact">contact</label></p>
			<p style="text-align: center;"> <input type="text" id="contact" name="contact" value="<?php echo"$contact"; ?>"><br></p>
                
			<p style="text-align: center;"> <label for="address">address</label></p>
			<p style="text-align: center;">  <input type="text" id="address" name="address" value="<?php echo"$address"; ?>"><br></p>
                
			<p style="text-align: center;"> <label for="name">Name</label></p>
			<p style="text-align: center;">  <input type="text" id="name" name="name" value="<?php echo"$name"; ?>"><br><br><br></p>
                
			<p style="text-align: center;">  <input type='hidden' name='poc_id' value="<?php echo"$poc_id"; ?>"></p>
			<p style="text-align: center;"> <input type="submit" value="submit" name="submit"></p>
                
            </form>
		</div>
	<script>
		 function logout() {
        // Redirect to homepage.html
        window.location.href = "index.html";
    }
		function openTab(evt, tabName) {
		  var i, tabcontent, tablinks;
		  tabcontent = document.getElementsByClassName("tabcontent");
		  for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		  }
		  tablinks = document.getElementsByClassName("tablinks");
		  for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		  }
		  document.getElementById(tabName).style.display = "block";
		  evt.currentTarget.className += " active";
		}

		// Get the element with id="defaultOpen" and click on it
		document.getElementById("defaultOpen").click();
	</script>
</body>
</html>