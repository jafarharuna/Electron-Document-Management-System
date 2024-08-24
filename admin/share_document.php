<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Ensure this file contains database connection setup

// Fetch user's documents
$userid = $_SESSION['userid'];
$documents = [];
$sql = "SELECT id, dpath FROM Document WHERE downer = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing documents query: " . $conn->error);
}
$stmt->bind_param("s", $userid);
if (!$stmt->execute()) {
    die("Error executing documents query: " . $stmt->error);
}
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $documents[] = $row;
}
$stmt->close();

// Fetch all users for recipient selection
$users = [];
$sql = "SELECT uname FROM Users";
$result = $conn->query($sql);
if (!$result) {
    die("Error fetching users: " . $conn->error);
}
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doc_id = $_POST['doc_id'];
    $receiver = $_POST['receiver'];
    $sdate = date('Y-m-d');
    $stime = date('H:i:s');

    // Prepare and execute insert statement
    $sql = "INSERT INTO share (ssender, sreceiver, sdoc, sdate, stime) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing insert statement: " . $conn->error);
    }
    $stmt->bind_param("ssiss", $userid, $receiver, $doc_id, $sdate, $stime);
    if ($stmt->execute()) {
        // Success: JavaScript alert and redirect
        $success = true;
    } else {
        echo "<div class='alert alert-danger'>Error sharing document: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>:: Aero Bootstrap4 Admin :: Home</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon"> <!-- Favicon-->
    <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/jvectormap/jquery-jvectormap-2.0.3.min.css"/>
    <link rel="stylesheet" href="assets/plugins/charts-c3/plugin.css"/>
    <link rel="stylesheet" href="assets/plugins/morrisjs/morris.min.css" />
    <!-- Custom Css -->
    <link rel="stylesheet" href="assets/css/style.min.css">
    
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 400px;
            text-align: center;
            position: relative;
        }
        .modal-header {
            margin-bottom: 15px;
        }
        .modal-header h2 {
            margin: 0;
        }
        .modal-body {
            margin-bottom: 15px;
        }
        .modal-footer {
            text-align: right;
        }
        .modal-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
  <!-- Navbar -->
  <?php include 'sidebar.php'; ?>
    <section class="content">

    <!-- Main content -->
    <div class="container">
        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        <div class="card">
            <div class="card-header">
                <h3>Share Document</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="doc_id">Select Document</label>
                        <select id="doc_id" name="doc_id" class="form-control" required>
                            <option value="" disabled selected>Select a document</option>
                            <?php foreach ($documents as $doc): ?>
                                <option value="<?php echo htmlspecialchars($doc['id']); ?>"><?php echo htmlspecialchars($doc['dpath']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="receiver">Select Recipient</label>
                        <select id="receiver" name="receiver" class="form-control" required>
                            <option value="" disabled selected>Select a user</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo htmlspecialchars($user['uname']); ?>"><?php echo htmlspecialchars($user['uname']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Share Document</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Success!</h2>
            </div>
            <div class="modal-body">
                <p>Document shared successfully!</p>
            </div>
            <div class="modal-footer">
                <button class="modal-button" id="closeModal">OK</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <?php if (isset($success) && $success): ?>
        <script>
            // Show the modal
            document.getElementById('successModal').style.display = 'flex';
            
            // Redirect after 3 seconds
            setTimeout(function() {
                window.location.href = "share_document.php";
            }, 3000); // 3000 milliseconds = 3 seconds

            // Optional: Hide the modal and redirect when OK is clicked
            document.getElementById('closeModal').onclick = function() {
                window.location.href = "share_document.php";
            };
        </script>
    <?php endif; ?>
</body>
</html>
