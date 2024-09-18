<?php
// Include the database connection file
include "connection.php";

// Start the session
session_start();

// Check if the donor ID is set in the session
if(isset($_SESSION['donor_id'])) {
    $donor_id = $_SESSION['donor_id'];

    // Check if the form data is submitted
    if(isset($_POST['next'])) {
        // Retrieve form data
        $num_donations = $_POST['num_donations'];
        $prep_time = $_POST['prep_time'];
        $prep_date = $_POST['prep_date'];

        // Insert donation record into donations table
        $stmt = $conn->prepare("INSERT INTO donations (donor_id, status) VALUES (?, 0)");
        $stmt->bind_param("i", $donor_id);

        if ($stmt->execute()) {
            // Retrieve the auto-incremented donation_id
            $donation_id = $stmt->insert_id;

            // Store the donation_id in the session
            $_SESSION['donation_id'] = $donation_id;

            // Redirect to the page for submitting food donation details
            header("Location: submit_food_donation.php?num_donations=$num_donations&prep_time=$prep_time&prep_date=$prep_date");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
} else {
    echo "Donor ID not set in session.";
}

// Close the database connection
$conn->close();
?>
