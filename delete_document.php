<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Handle document deletion
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert document into RecycledDocuments table
        $insertRecycleSql = "INSERT INTO RecycledDocuments (id, uname) SELECT id, downer FROM Document WHERE id = ?";
        $insertRecycleStmt = $conn->prepare($insertRecycleSql);
        if ($insertRecycleStmt === false) {
            throw new Exception("Error preparing the statement: " . $conn->error);
        }
        $insertRecycleStmt->bind_param("i", $delete_id);
        if (!$insertRecycleStmt->execute()) {
            throw new Exception("Error executing the statement: " . $insertRecycleStmt->error);
        }
        $insertRecycleStmt->close();

        // Delete document from Document table
        $deleteSql = "DELETE FROM Document WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        if ($deleteStmt === false) {
            throw new Exception("Error preparing the statement: " . $conn->error);
        }
        $deleteStmt->bind_param("i", $delete_id);
        if (!$deleteStmt->execute()) {
            throw new Exception("Error executing the statement: " . $deleteStmt->error);
        }
        $deleteStmt->close();

        // Commit transaction
        $conn->commit();
        
        // Redirect to recycle bin page after successful deletion
        header("Location: recycle_bin.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        die("Error processing the request: " . $e->getMessage());
    }
}

// Fetch documents accessible by the user
$sql = "SELECT id, dname, dtitle, dsize1 AS dsize, dtype, ddept, downer, daccess, ddate, dtime, dpath 
        FROM Document 
        WHERE downer = ? OR daccess = 'Public' OR (daccess = 'Department' AND ddept = ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

// Bind parameters
$ddept = isset($_SESSION['ddept']) ? $_SESSION['ddept'] : 'Unknown';
$stmt->bind_param("ss", $_SESSION['userid'], $ddept);

if ($stmt->execute()) {
    $result = $stmt->get_result();
} else {
    die("Error executing the statement: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>

<?php 
include 'navbar.php';
?>
  <div class="container">
        <div class="card">
            <div class="card-header">
    <div class="container mt-4">
        <h2>Documents</h2>
        <?php if (isset($result) && $result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Title</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Department</th>
                        <th>Owner</th>
                        <th>Access</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($doc = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($doc['id']); ?></td>
                            <td><?php echo htmlspecialchars($doc['dname']); ?></td>
                            <td><?php echo htmlspecialchars($doc['dtitle']); ?></td>
                            <td><?php echo htmlspecialchars($doc['dsize']); ?></td>
                            <td><?php echo htmlspecialchars($doc['dtype']); ?></td>
                            <td><?php echo htmlspecialchars($doc['ddept']); ?></td>
                            <td><?php echo htmlspecialchars($doc['downer']); ?></td>
                            <td><?php echo htmlspecialchars($doc['daccess']); ?></td>
                            <td><?php echo htmlspecialchars($doc['ddate']); ?></td>
                            <td><?php echo htmlspecialchars($doc['dtime']); ?></td>
                            <td>
                                <form action="delete_document.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($doc['id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this document?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No documents available.</p>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>