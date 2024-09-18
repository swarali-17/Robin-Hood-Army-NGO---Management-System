<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>poc login</title>
    <link rel="stylesheet" type="text/css" href="pocLogin.css"> 
</head>
<body>
    <main>
        <section class="container">
		<h2>Welcome back POC!</h2>
            <h3>Login</h3>
            <form method="POST">
                
                <label for="username" class="label">Username</label><br> <!-- Added class="label" -->
                <input type="text" id="username" name="username" class="input-group" required><br><br> <!-- Added class="input-group" -->

                <label for="password" class="label">Password</label><br> <!-- Added class="label" -->
                <input type="password" id="password" name="password" class="input-group" placeholder="Minimum 9 characters" required><br><br> <!-- Added class="input-group" -->
                
                <input type="submit" class="button" value="Submit" name="submit" required>
                
            </form>
            <?php
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            include "connection.php";

            if(isset($_POST["submit"]))
            {
            
                $username = $_POST['username'];
                $password = $_POST['password'];
                $sql = "SELECT * FROM pocs WHERE username = '$username' and password = '$password'";
            
                $result = $conn->query($sql);
                $num_rows = mysqli_num_rows($result);
                if($num_rows == 1)
                {
                    session_start();
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    header("Location: poc_Dashboard.php");
                    exit;
                    
                }
                else
                {
                    echo "Invalid Username or password. Please Re-enter";
                }
            }
            ?>
         </section>
    </main>
</body>
</html>
