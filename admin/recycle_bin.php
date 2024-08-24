<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Fetch deleted documents
$documents = [];
$sql = "SELECT id, uname FROM RecycledDocuments WHERE restored = 0";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching deleted documents: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $documents[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore'])) {
    $doc_id = intval($_POST['restore']);
    
    // Restore document
    $restoreSql = "UPDATE Document SET deleted = 0 WHERE id = ?";
    $restoreStmt = $conn->prepare($restoreSql);
    
    if ($restoreStmt === false) {
        die("Error preparing the restore statement: " . $conn->error);
    }
    
    $restoreStmt->bind_param("i", $doc_id);
    
    if ($restoreStmt->execute()) {
        // Mark as restored in the recycle bin
        $updateRecycleSql = "UPDATE RecycledDocuments SET restored = 1 WHERE id = ?";
        $updateRecycleStmt = $conn->prepare($updateRecycleSql);
        
        if ($updateRecycleStmt === false) {
            die("Error preparing the update recycle statement: " . $conn->error);
        }
        
        $updateRecycleStmt->bind_param("i", $doc_id);
        
        if ($updateRecycleStmt->execute()) {
            echo "<div class='alert alert-success'>Document restored successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating recycle bin: " . $updateRecycleStmt->error . "</div>";
        }
        
        $updateRecycleStmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error restoring document: " . $restoreStmt->error . "</div>";
    }
    
    $restoreStmt->close();
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
        .modal-body img {
            max-width: 80%;
        }
    </style>
</head>
<body>
  <!-- Navbar -->
  <?php include 'sidebar.php'; ?>
    <section class="content">
    <div class="container">
        <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
        <div class="card">
            <div class="card-header">
                <h3>Recycle Bin</h3>
            </div>
            <div class="card-body">
                <?php if (count($documents) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($doc['id']); ?></td>
                                <td><?php echo htmlspecialchars($doc['uname']); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="restore" value="<?php echo htmlspecialchars($doc['id']); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Restore</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No documents in the recycle bin.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
