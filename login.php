<?php
    session_start();

    // Database connection
    $servername = "localhost"; // Change if your database server is different
    $username = "root"; // Change to your database username
    $password = ""; // Change to your database password
    $dbname = "electronicdocument"; // Your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if POST data is set
        if (isset($_POST['uname']) && isset($_POST['upass'])) {
            // Sanitize user input
            $uname = htmlspecialchars($_POST['uname']);
            $upass = htmlspecialchars($_POST['upass']);

            // Prepare and execute query
            $stmt = $conn->prepare("SELECT upass FROM users WHERE uname = ?");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("s", $uname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                $user = $result->fetch_assoc();
                if ($user && password_verify($upass, $user['upass'])) {
                    // Successful login
                    echo "<script>
                            setTimeout(function() {
                                alert('Welcome, " . addslashes($uname) . "!');
                                window.location.href = 'dashboard.php';
                            }, 2000);
                          </script>";
                } else {
                    // Failed login
                    echo "<script>
                            setTimeout(function() {
                                alert('Invalid username or password.');
                                window.location.href = 'login.php';
                            }, 2000);
                          </script>";
                }
            } else {
                // Failed to execute the query
                die("Query execution failed: " . $stmt->error);
            }

            $stmt->close();
            $conn->close();
        }
    }
    ?>
    
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 2em;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            max-width: 100%;
        }
        h2 {
            margin-bottom: 1em;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1em;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5em;
            color: #666;
        }
        .form-group input {
            width: 100%;
            padding: 0.8em;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            border-color: #0056b3;
            outline: none;
        }
        .form-group button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 0.8em;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
            margin-top: 0.5em;
        }
        .form-group button:hover {
            background-color: #004494;
        }
        .form-group .reset-button {
            background-color: #6c757d;
        }
        .form-group .reset-button:hover {
            background-color: #5a6268;
        }
        .form-group .create-account {
            background-color: #17a2b8;
        }
        .form-group .create-account:hover {
            background-color: #138496;
        }
        .message {
            text-align: center;
            margin-top: 1em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="login_action.php">
            <div class="form-group">
                <label for="uname">Username</label>
                <input type="text" id="uname" name="uname" required>
            </div>
            <div class="form-group">
                <label for="upass">Password</label>
                <input type="password" id="upass" name="upass" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
                <button type="reset" class="reset-button">Reset</button>
            </div>
            <div class="form-group">
                <button type="button" class="create-account" onclick="window.location.href='register.php'">Create an Account</button>
            </div>
        </form>
        <div class="message"><?php if (isset($login_message)) echo $login_message; ?></div>
    </div>

 
</body>
</html>
