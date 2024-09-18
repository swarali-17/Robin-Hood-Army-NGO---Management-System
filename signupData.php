<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include "connection.php";

// Check if form is submitted
if(isset($_POST["submit"])) {
    // Retrieve form data
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contactNumber = $_POST['contactNumber'];
    $aadhaarNo = $_POST['aadhaarNo'];
    $pincode = $_POST['pincode'];
    $residential_address = $_POST['residential_address'];
    $password = $_POST['password'];

    // Check if username already exists
    $sql1 = "SELECT * FROM robins WHERE username = '$username'";
    $result = $conn->query($sql1);
    
    if ($result->num_rows > 0) {
        echo "Username already exists";
    } 
    else {
        // Fetch POC ID based on provided pincode
        $sql_fetch_poc_id = "SELECT poc_id FROM pocs WHERE pincode = '$pincode'";
        $result_fetch_poc_id = $conn->query($sql_fetch_poc_id);

        // Check if POC with provided pincode exists
        if ($result_fetch_poc_id->num_rows > 0) {
            $row = $result_fetch_poc_id->fetch_assoc();
            $poc_id = $row['poc_id'];

            // Insert new user record with fetched POC ID
            $sql2 = "INSERT INTO robins (username, password, name, residential_address, email, contactNumber, aadhaarNo, pincode, poc_id)
                                VALUES ('$username', '$password', '$name', '$residential_address', '$email', '$contactNumber', '$aadhaarNo', '$pincode', '$poc_id')";

            if ($conn->query($sql2) === TRUE) {
                echo "<h3> Registered Successfully ! Please login now to continue</h3><br>";
			    echo "<a href= 'robin_Login.php'>Login</a>";
              
                } 
            
             else {
                echo "Error: " . $sql2 . "<br>" . $conn->error;
            }
        }
    }
}
?>
