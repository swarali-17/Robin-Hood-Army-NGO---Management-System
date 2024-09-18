<?php
// Include the database connection file
include "connection.php";

// Start the session
session_start();

// Check if the donor ID is set in the session
if(isset($_SESSION['donor_id'])) {
    $donor_id = $_SESSION['donor_id'];

    // Check if the donation ID is already set in the session
    if(isset($_SESSION['donation_id'])) {
        $donation_id = $_SESSION['donation_id'];
    } /*else {
        // Generate a new donation_id and store it in the session
        $donation_id = generateDonationID($conn);
        if($donation_id) {
            $_SESSION['donation_id'] = $donation_id;
        } else {
            echo "Failed to generate donation ID.";
        }
    }*/

    // SQL query to fetch donor information
    $sql = "SELECT * FROM donors WHERE donor_id = '$donor_id'";

    // Execute the SQL query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        // Check if any rows were returned
        if ($result->num_rows > 0) {
            // Fetch the donor's information
            $row = $result->fetch_assoc();
            $username = $row['username'];
            $password = $row['password'];
            $name = $row['name'];
            $email = $row['email'];
            $address = $row['address'];

            // Display the donor's name
          //  echo "<h3>Welcome to Your Dashboard, $name</h3>";
            
        } else {
            echo "No donor found with ID: $donor_id";
        }
    } else {
        echo "Error fetching donor information: " . $conn->error;
    }
} else {
    echo "Donor ID not set in session.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard</title>
    <link rel="stylesheet" type="text/css" href="donorDash.css">
</head>
<body>
    <header>
    <h2>Welcome Donor! </h2>

    <div class="tab">
		  	<button class="tablinks" onclick="openTab(event, 'Home')" id="defaultOpen">Home</button>
		  	<button class="tablinks" onclick="openTab(event, 'MakeDonation')">Make Donation</button>
		  	<button class="tablinks" onclick="openTab(event, 'ViewPreviousDonations')">View Previous Donations</button>
		  	<button class="tablinks" onclick="openTab(event, 'AccountSettings')" >Account Settings</button>
            <button onclick="logout()">Logout</button>
	</div>
    
    </header>
    
    <div id="Home" class="tabcontent">
     <h3> How can you help?</h3>
      <p style="text-align: center;"><strong>Contribute Food<strong></p>
      
       <p style="text-align: center;">If you manage a restaurant or generally want to contribute regular meals from your family or workplace, let’s connect.</p>
       <img src="image.png" alt="Image Description" style="float: center; max-width: 600px;">
        <!-- Display donor information -->
        <!-- <p><strong>Username:</strong> <?php echo isset($username) ? $username : ""; ?></p>
        <p><strong>Name:</strong> <?php echo isset($name) ? $name : ""; ?></p>
        <p><strong>Email:</strong> <?php echo isset($email) ? $email : ""; ?></p>
        <p><strong>Address:</strong> <?php echo isset($address) ? $address : ""; ?></p> -->
    </section>
    </div>

    <div id="MakeDonation" class="tabcontent mini-box">
    <h2> <p style="text-align: center;">Make Donation</p></h2>
    <form action="process_donation.php" method="POST">
        
        <label for="num_donations">How many food donations do you want to make?</label>
        <input type="number" id="num_donations" name="num_donations" required><br><br>

        <label for="prep_time">Preparation Time:</label>
        <input type="time" id="prep_time" name="prep_time" required><br><br>

        <label for="prep_date">Preparation Date:</label>
        <input type="date" id="prep_date" name="prep_date" required><br><br>

        <input type="submit" name="next" value="Next">
    </form>
</div>


<div id="ViewPreviousDonations" class="tabcontent">
    <?php
        // Include the database connection file
        include "connection.php";

        // Start the session
        @session_start();

        // Check if the donor is logged in
        if (!isset($_SESSION['donor_id'])) {
            // Redirect to the login page if the donor ID is not set
            header("Location: donorLogin.php");
            exit();
        }

        // Get the donor ID from the session
        $donor_id = $_SESSION['donor_id'];

        // Query to fetch previous donations of the logged-in donor with additional fields for Food Name, Quantity, and Date
        $sql = "SELECT d.donation_id, d.status, d.robinID, f.food_name, f.quantity, f.date
                FROM donations d
                JOIN food f ON d.donation_id = f.donation_id
                WHERE d.donor_id = ?";
                
        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("i", $donor_id);

            // Execute the query
            $stmt->execute();

            // Store the result
            $result = $stmt->get_result();

            // Check if there are any previous donations
            if ($result->num_rows > 0) {
                // Display the table header
                echo "<table class='donation-table'>";
                echo "<tr><th>Donation ID</th><th>Status</th><th>Food Name</th><th>Quantity</th><th>Date</th><th>Robin Details</th></tr>";
                
                // Display each donation record
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['donation_id'] . "</td>";
                    echo "<td>";
                    // Display the status in green bold for 'Completed' and red bold for 'Pending'
                    echo ($row['status'] ? "<strong style='color:green;'>Completed</strong>" : "<strong style='color:red;'>Pending</strong>");
                    echo "</td>";
                    echo "<td>" . $row['food_name'] . "</td>";
                    echo "<td>" . $row['quantity'] . "</td>";
                    echo "<td>" . $row['date'] . "</td>"; // Use 'date' column for donation date

                    // Fetch Robin details using the RobinID from the donations table
                    $robinID = $row['robinID'];
                    $robinDetailsQuery = "SELECT name, contactNumber FROM robins WHERE robinID = ?";
                    $robinDetailsStmt = $conn->prepare($robinDetailsQuery);
                    $robinDetailsStmt->bind_param("i", $robinID);
                    $robinDetailsStmt->execute();
                    $robinDetailsResult = $robinDetailsStmt->get_result();

                    if ($robinDetailsResult->num_rows > 0) {
                        $robinDetails = $robinDetailsResult->fetch_assoc();
                        echo "<td>" . $robinDetails['name'] . " (" . $robinDetails['contactNumber'] . ")</td>";
                    } else {
                        echo "<td>No details available</td>";
                    }

                    echo "</tr>";
                }

                // Close the table
                echo "</table>";
            } else {
                echo "No previous donations found.";
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            // Error preparing statement
            echo "Error preparing statement: " . $conn->error;
        }

        // Close the database connection
        $conn->close();
    ?>


</div>

    <div id="AccountSettings" class="tabcontent mini-box">
			
    <h2> <p style="text-align: center;"> Update details</p></h2>
			
            <form  method="POST" action='editProfile.php' >


            <p ><label for="NewUsername">Username</label></p>
            <p style="text-align: center;"> <input type="text" id="NewUsername" name="NewUsername" value="<?php echo"$username"; ?>"></p>

            <p ><label for="password">Password</label></p>
            <p style="text-align: center;">   <input type="text" id="password" name="password" value=""></p><br>
           		 
            <p style="text-align: center;"> <input type='hidden' name='donor_id' value="<?php echo"$donor_id"; ?>"></p>
            <p style="text-align: center;">   <input type="submit" value="Submit" name="submit"></p>
                
            </form>

            <br><p style="text-align: center;">---------------------------------- OR ----------------------------------</p><br>
            <h2><button onclick= "deleteAcc()" > Delete Account</button></h2>
            
</div>
    <footer>
        <p>You are making a difference!</p>
        <p>Thank you for your generosity.</p>
    </footer>
    <script>
         function logout() {
        // Redirect to homepage.html
        window.location.href = "index.html";
    }
   
    	function deleteAcc() {
        var result = confirm("Are you sure you want to delete your account?");
        if (result) {
            // If user confirms, redirect to PHP script to delete account
            window.location.href = 'delete_account.php?donor_id=<?php echo $donor_id; ?>';
        }
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