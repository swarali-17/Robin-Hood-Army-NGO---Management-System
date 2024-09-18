<!DOCTYPE html>
	<html lang="en">
	<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>robin_dashboard</title>
	<link rel="stylesheet" type="text/css" href="Robin_dashboard.css"> 
	</head>
	<body>
	<header>
		<h3>Robin Dashboard </h3>

        
		<div class="tab">
			<button class="tablinks" onclick="openTab(event, 'Home')" id="defaultOpen">Home</button>
			<button class="tablinks" onclick="openTab(event, 'Completed Donations')">Completed Donations</button>
			<button class="tablinks" onclick="openTab(event, 'Pending Donations')">Pending Donations</button>
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

    // Retrieve the details of the robin
    $sql = "SELECT * FROM robins WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    $robin_row = $result->fetch_assoc();
    $pincode = $robin_row['pincode'];//$pincode variable now stores the pincode
    $contactNumber = $robin_row['contactNumber'];
    $residential_address = $robin_row['residential_address'];
    $robinId = $robin_row['robinId'];
    $name = $robin_row['name'];

    $poc_id = $robin_row['poc_id']; // Assuming this is the column name in robins table

    ?>
    <div class="robin-details">
        <h4>Your Details:</h4>
        <ul>
            <li><strong>ROBIN ID:</strong> <?php echo $robinId; ?></li>
            <li><strong>Name:</strong> <?php echo $name; ?></li>
            <li><strong>Contact Number:</strong> <?php echo $contactNumber; ?></li>
            <li><strong>Pincode:</strong> <?php echo $pincode; ?></li>
        </ul>
    </div>
    <hr>

    <?php
    // Retrieve the POC details assigned to that robin
    $poc_sql = "SELECT * FROM pocs WHERE poc_id = '$poc_id'";
    $poc_result = $conn->query($poc_sql);
    $poc_row = $poc_result->fetch_assoc();
    $poc_name = $poc_row['name'];
    $poc_contact = $poc_row['contact'];
    $poc_pincode = $poc_row['pincode'];
    ?>
    <div class="poc-details">
        <h4>Center Head:</h4>
        <ul>
            <li><strong>POC ID:</strong> <?php echo $poc_id; ?></li>
            <li><strong>Name:</strong> <?php echo $poc_name; ?></li>
            <li><strong>Contact Number:</strong> <?php echo $poc_contact; ?></li>
            <li><strong>Pincode:</strong> <?php echo $poc_pincode; ?></li>
        </ul>
    </div>
</div>


        <div id="Completed Donations" class="tabcontent">
    <h4>Below are your completed donations:</h4>
    <?php
    $sql_completedDonations = "SELECT donation_id, status FROM donations WHERE robinId = '{$robin_row['robinId']}' AND status = 1";
    $result_completedDonations = $conn->query($sql_completedDonations);

    // Check if any completed donations exist
    if ($result_completedDonations->num_rows > 0) {
        // Output data of each row
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Donation ID</th>";
        echo "<th>Status</th>";
        echo "<th>Food Details</th>";
        echo "<th>Donor Details</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result_completedDonations->fetch_assoc()) {
            $donation_id = $row["donation_id"];
            $status = $row["status"];

            // Query to fetch food details
            $sql_food_details = "SELECT food_name, quantity FROM food WHERE donation_id = '$donation_id'";
            $result_food_details = $conn->query($sql_food_details);

            // Query to fetch donor details
            $sql_donor_details = "SELECT name, contact, address, pincode FROM donors WHERE donor_id = (SELECT donor_id FROM donations WHERE donation_id = '$donation_id')";
            $result_donor_details = $conn->query($sql_donor_details);

            // Output donation details in table rows
            echo "<tr>";
            echo "<td>" . $donation_id . "</td>";
            echo "<td><p style='color:green;'>Completed</p></td>";
            echo "<td>";
            echo "<ul>";
            while ($food_row = $result_food_details->fetch_assoc()) {
                echo "<li>";
                foreach ($food_row as $key => $value) {
                    echo $key . " : " . $value . "<br>";
                }
                echo "<br>";
                echo "</li>";
            }
            echo "</ul>";
            echo "</td>";
            echo "<td>";
            // Fetch donor details and display them
            $donor_details = $result_donor_details->fetch_assoc();
            foreach ($donor_details as $key => $value) {
                echo $key . " : " . $value . "<br><br>";
            }
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        // If no completed donations found
        echo "<p>Sorry, No completed donations found.</p>";
    }
    ?>
</div>



<div id="Pending Donations" class="tabcontent">
    <h4>Below are pending donations details:</h4>
    <?php
    $sql_completedDonations = "SELECT donation_id, status FROM donations WHERE robinId = '{$robin_row['robinId']}' AND status = 0";
    $result_completedDonations = $conn->query($sql_completedDonations);

    // Check if any completed donations exist
    if ($result_completedDonations->num_rows > 0) {
        // Output data of each row
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Donation ID</th>";
        echo "<th>Status</th>";
        echo "<th>Food Details</th>";
        echo "<th>Donor Details</th>";
        echo "<th>Mark Completed</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = $result_completedDonations->fetch_assoc()) {
            $donation_id = $row["donation_id"];
            $status = $row["status"];

            // Query to fetch food details
            $sql_food_details = "SELECT food_name, quantity FROM food WHERE donation_id = '$donation_id'";
            $result_food_details = $conn->query($sql_food_details);

            // Query to fetch donor details
            $sql_donor_details = "SELECT name, contact, address, pincode FROM donors WHERE donor_id = (SELECT donor_id FROM donations WHERE donation_id = '$donation_id')";
            $result_donor_details = $conn->query($sql_donor_details);

            // Output donation details in table rows
            echo "<tr>";
            echo "<td>" . $donation_id . "</td>";
            echo "<td><p style='color:red;'>Pending</p></td>";
            
            echo "<td><ul>";
            while ($food_row = $result_food_details->fetch_assoc()) {
                echo "<li>";
                foreach ($food_row as $key => $value) {
                    echo $key . " : " . $value . "<br>";
                }
                echo "<br>";
                echo "</li>";
            }
            echo "</ul></td>";

            echo "<td>";
            // Fetch donor details and display them
            $donor_details = $result_donor_details->fetch_assoc();
            foreach ($donor_details as $key => $value) {
                echo $key . " : " . $value . "<br><br>";
            }
            echo "</td>";
        
            echo "<td><form method='post' action='change_status.php'>
                            <input type='hidden' name='donation_id' value='$donation_id'>
                            <button type='submit' name='completed'>Mark as Completed</button>
                          </form></td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        // If no completed donations found
        echo "<p>Good job! No pending donations found.</p>";
    }
    ?>
</div>



<!-- Display the chart using CanvasJS -->
<div id="Analytics" class="tabcontent">

<?php
// Make sure to include the necessary connection and configuration files

// Fetch the robin ID for whom you want to display the graph
$robinId = $robin_row['robinId'];

// Initialize the dataPoints array
$dataPoints = array();

// Query to fetch the count of completed donations grouped by months for the specified robin
$sql_donations = "SELECT MONTH(food.date) AS month, COUNT(donations.donation_id) AS donations_count
                  FROM donations
                  INNER JOIN food ON donations.donation_id = food.donation_id
                  WHERE donations.robinId = '$robinId' AND donations.status = '1'
                  GROUP BY MONTH(food.date)";

// Execute the query
$result_donations = $conn->query($sql_donations);

// Check if there are any results
if ($result_donations->num_rows > 0) {
    // Loop through each row and fetch the data
    while ($row = $result_donations->fetch_assoc()) {
        // Format the data and add it to the dataPoints array
        $dataPoints[] = array("y" => $row['donations_count'], "label" => date('M', mktime(0, 0, 0, $row['month'], 1)));
    }
}
?>

    <script>
        window.onload = function() {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Monthly Completed Donations"
                },
                axisY: {
                    title: "No. of Completed Donations"
                },
                axisX: {
                    title: "Month"
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
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</div>


		<div id="Account Settings" class="tabcontent mini-box">
			<p style="text-align: center;"> <strong>Edit the fields which you want to update </p>
			
			<form  method="POST"action='robin_account_settings.php'>
				
            <p style="text-align: center;"><label for="NewUsername">username  </label></p>
            <p style="text-align: center;"><input type="text" id="NewUsername" name="NewUsername" value="<?php echo"$username"; ?>"><br></p>

			<p style="text-align: center;">	<label for="password">password  </label></p>
			<p style="text-align: center;">	<input type="text" id="password" name="password" value="<?php echo"$password"; ?>"><br></p>
				
            <p style="text-align: center;"><label for="contactNumber">contact</label></p>
            <p style="text-align: center;">   <input type="text" id="contactNumber" name="contactNumber" value="<?php echo"$contactNumber"; ?>"><br></p>
                
            <p style="text-align: center;">   <label for="residential_address">address</label></p>
            <p style="text-align: center;">  <input type="text" id="residential_address" name="residential_address" value="<?php echo"$residential_address"; ?>"><br></p>
                
            <p style="text-align: center;">  <label for="name">Name</label></p>
            <p style="text-align: center;">  <input type="text" id="name" name="name" value="<?php echo"$name"; ?>"><br></p>

            <p style="text-align: center;"><input type='hidden' name='robinId' value="<?php echo"$robinId"; ?>"></p>
			<p style="text-align: center;">	<input type="submit" value="submit" name="submit"></p>
				
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
