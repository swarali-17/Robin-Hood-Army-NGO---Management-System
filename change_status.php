<?php
// Include the database connection
include "connection.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donation_id'])) {
    // Get the donation_id from the form
    $donation_id = $_POST['donation_id'];

    // Update the status of the donation to 'done' in the donations table
    $sql_update_status = "UPDATE donations SET status = 1 WHERE donation_id = '$donation_id'";
    if ($conn->query($sql_update_status) === TRUE) {
        // Status updated successfully, redirect back to robin_Dashboard.php
        header("Location: robin_Dashboard.php");
        exit();
    } else {
        // Error updating status
        echo "Error updating status: " . $conn->error;
    }
} else {
    // Redirect to robin_Dashboard.php if accessed directly
    header("Location: robin_Dashboard.php");
    exit();
}
?>
