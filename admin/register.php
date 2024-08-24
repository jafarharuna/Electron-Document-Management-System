<?php
session_start();
include 'config.php'; // Include your database connection

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
    if (empty($uname) || empty($upass) || empty($confirm_pass) || empty($ulname) || empty($uoname) || empty($uphone) || empty($udpart) || empty($uemail)) {
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
        $sql = "SELECT id FROM users WHERE uname = ? OR uemail = ?";
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
            $sql = "INSERT INTO users (uname, upass, ulname, uoname, uphone, udept, uemail, udate) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            // Bind parameters and execute statement
            $stmt->bind_param("sssssss", $uname, $upass, $ulname, $uoname, $uphone, $udpart, $uemail);
            if ($stmt->execute()) {
                echo "<script>
                        alert('Registration successful!');
                        setTimeout(function() {
                            window.location.href = 'login.php';
                        }, 2000); // Redirect after 2 seconds
                      </script>";
            } else {
                echo "<div class='alert alert-danger'>Registration failed: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your custom CSS file -->
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="registration-form">
                    <h2 class="text-center">Registration Form</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="uname">Username:</label>
                            <input type="text" id="uname" name="uname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="upass">Password:</label>
                            <input type="password" id="upass" name="upass" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_pass">Confirm Password:</label>
                            <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="ulname">First Name:</label>
                            <input type="text" id="ulname" name="ulname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="uoname">Last Name:</label>
                            <input type="text" id="uoname" name="uoname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="uphone">Phone Number:</label>
                            <input type="text" id="uphone" name="uphone" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="udpart">Department:</label>
                            <select id="udpart" name="udpart" class="form-control" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?php echo htmlspecialchars($department); ?>"><?php echo htmlspecialchars($department); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="uemail">Email:</label>
                            <input type="email" id="uemail" name="uemail" class="form-control" required>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Create Account</button>
                            <button type="reset" class="btn btn-default">Reset</button>
                          <a href="http://localhost/document/login.php" class="btn btn-primary">Login  </a> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>