<?php
include "connection.php";

if(isset($_GET['donor_id'])) {
    $donor_id = $_GET['donor_id'];
    
    // Check if there are pending donations
    $sql_donation = "SELECT COUNT(*) AS num_pending_donations FROM donations WHERE donor_id = $donor_id AND status = 0";
    $result_donation = $conn->query($sql_donation);
    
    if($result_donation) {
        $row = $result_donation->fetch_assoc();
        $num_pending_donations = $row['num_pending_donations'];
        
        if($num_pending_donations > 0) {
            echo "Cannot delete your account since you have some pending donations. You can delete when all donations are complete.";
            echo "<br><a href='donorD.php'>Go back to dashboard</a>";
        } else {
            // No pending donations, proceed with account deletion
            $sql = "DELETE FROM donors WHERE donor_id = $donor_id";
            if($conn->query($sql) === true) { 
                echo "Record was deleted successfully."; 
                echo "<br><a href='index.html'>Go back to homepage</a>";
            } else { 
                echo "ERROR: Could not execute $sql. " . $conn->error; 
            }
        }
    } else {
        echo "ERROR: Could not execute $sql_donation. " . $conn->error;
    }
}
?>
