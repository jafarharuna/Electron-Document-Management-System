<?php
session_start();
include 'config.php'; // Include your database connection

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = trim($_POST['uname']); // Trim to remove any extra spaces
    $upass = $_POST['upass'];

    // Prepare SQL statement to fetch user by username
    $sql = "SELECT id, upass FROM users WHERE uname = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute statement
    $stmt->bind_param("s", $uname); // "s" specifies the type (string) for $uname
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with the provided username exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Directly compare the provided password with the stored plain text password
        if ($upass === $user['upass']) {
            // Password is correct; set session variables and redirect
            $_SESSION['userid'] = $user['id'];
            echo "<script>
                    setTimeout(function() {
                        alert('Login successful!');
                        window.location.href = 'dashboard.php';
                    }, 200); // Redirect after 2 seconds
                  </script>";
        } else {
            // Password is incorrect
            echo "<script>
                    setTimeout(function() {
                        alert('Invalid password.');
                        window.location.href = 'login.php';
                    }, 200); // Redirect after 2 seconds
                  </script>";
        }
    } else {
        // No user found with that username
        echo "<script>
                setTimeout(function() {
                    alert('No user found with that username.');
                    window.location.href = 'login.php';
                }, 200); // Redirect after 2 seconds
              </script>";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Not a POST request
    echo "<script>
            setTimeout(function() {
                alert('Invalid request method.');
                window.location.href = 'login.php';
            }, 200); // Redirect after 2 seconds
          </script>";
}
?>
