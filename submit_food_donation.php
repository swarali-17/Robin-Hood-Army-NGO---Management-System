<?php
// Include the database connection file
include "connection.php";

// Start the session
session_start();

// Check if the donor ID and donation ID are set in the session
if(isset($_SESSION['donor_id']) && isset($_SESSION['donation_id'])) {
    $donor_id = $_SESSION['donor_id'];
    $donation_id = $_SESSION['donation_id'];

    // Check if the form data is submitted
    if(isset($_POST['submit'])) {
        // Retrieve form data for each food donation
        for ($i = 1; $i <= $_POST['num_donations']; $i++) {
            $food_name = $_POST['food_name_'.$i];
            $quantity = $_POST['quantity_'.$i];

            // Insert food donation details into the food table
            $stmt = $conn->prepare("INSERT INTO food (donation_id, food_name, quantity, prep_time, date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isiss", $donation_id, $food_name, $quantity, $_POST['prep_time'], $_POST['prep_date']);

            if ($stmt->execute()) {
                // Food donation details inserted successfully
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        }

        // Redirect to the home page after submitting all food donations
        header("Location: donorD.php");
        exit();
    }
} else {
    echo "Donor ID or Donation ID not set in session.";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Food Donation</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h2>Submit Food Donation</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <?php
        // Retrieve the number of food donations specified by the donor
        $num_donations = $_GET['num_donations'];

        // Display form for each food donation
        for ($i = 1; $i <= $num_donations; $i++) {
            echo "<label for='food_name_$i'>Food Name $i:</label>";
            echo "<input type='text' id='food_name_$i' name='food_name_$i' required><br><br>";
            echo "<label for='quantity_$i'>Quantity $i:</label>";
            echo "<input type='number' id='quantity_$i' name='quantity_$i' required><br><br>";
        }
        ?>
        <input type="hidden" name="num_donations" value="<?php echo $num_donations; ?>">
        <input type="hidden" name="prep_time" value="<?php echo $_GET['prep_time']; ?>">
        <input type="hidden" name="prep_date" value="<?php echo $_GET['prep_date']; ?>">
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>