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
        echo "<div class='alert alert-success'>Document shared successfully!</div>";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Document</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>