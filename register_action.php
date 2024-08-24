<?php
session_start();
include 'config.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$departments = ['Computer Science', 'Math Science', 'Statistics', 'Natural Science'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = trim($_POST['uname']);
    $upass = $_POST['upass'];
    $confirm_pass = $_POST['confirm_pass'];
    $ulname = trim($_POST['ulname']);
    $uoname = trim($_POST['uoname']);
    $uphone = trim($_POST['uphone']);
    $udpart = $_POST['udpart'];
    $uemail = trim($_POST['uemail']);

    // Basic validation
    $errors = [];
    if (empty($uname) || empty($upass) || empty($confirm_pass) || empty($ulname) || empty($uoname) || empty($uphone)) {
        $errors[] = "All fields are required.";
    }
    if ($upass !== $confirm_pass) {
        $errors[] = "Passwords do not match.";
    }
    if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (strlen($upass) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // Check for existing user
    if (empty($errors)) {
        $sql = "SELECT id FROM Users WHERE uname = ? OR uemail = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ss", $uname, $uemail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // User already exists
            echo "<script>
                    alert('User already registered with this username or email!');
                    setTimeout(function() {
                        window.location.href = 'register.php';
                    }, 1000); // Redirect after 1 second
                  </script>";
        } else {
            // No existing user, proceed with registration
            $hashed_pass = password_hash($upass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO Users (uname, upass, ulname, uoname, uphone, udept, uemail, udate) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sssssss", $uname, $hashed_pass, $ulname, $uoname, $uphone, $udpart, $uemail);
            if ($stmt->execute()) {
                echo "<script>
                        alert('Registration successful!');
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000); // Redirect after 2 seconds
                      </script>";
            } else {
                echo "<div class='alert alert-danger'>Registration failed: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
        $stmt->close();
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }

    $conn->close();
}
?>
