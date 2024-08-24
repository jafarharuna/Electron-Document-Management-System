<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid document ID.");
}

$id = intval($_GET['id']);

// Fetch document details
$sql = "SELECT * FROM Document WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $doc = $result->fetch_assoc();
    } else {
        die("Document not found.");
    }
} else {
    die("Error executing the statement: " . $stmt->error);
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update document details
    $dname = $_POST['dname'];
    $dtitle = $_POST['dtitle'];
    $daccess = $_POST['daccess'];
    $ddept = $_POST['ddept'];
    $dcomment = $_POST['dcomment'];

    $updateSql = "UPDATE Document SET dname = ?, dtitle = ?, daccess = ?, ddept = ?, dcomment = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);

    if ($updateStmt === false) {
        die("Error preparing the statement: " . $conn->error);
    }

    $updateStmt->bind_param("sssssi", $dname, $dtitle, $daccess, $ddept, $dcomment, $id);

    if ($updateStmt->execute()) {
        $_SESSION['update_success'] = true; // Set session variable to show the success modal
        $updateStmt->close();
        $conn->close();
        header("Location: edit_document.php?id=$id"); // Redirect to the same page to trigger modal
        exit();
    } else {
        echo "Error updating the document: " . $updateStmt->error;
    }

    $updateStmt->close();
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
    <!-- Main content -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Edit Document</h3>
            </div>
            <div class="card-body">
                <form action="edit_document.php?id=<?php echo htmlspecialchars($id); ?>" method="POST">
                    <div class="form-group">
                        <label for="dname">Document Name:</label>
                        <input type="text" class="form-control" id="dname" name="dname" value="<?php echo htmlspecialchars($doc['dname']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dtitle">Document Title:</label>
                        <input type="text" class="form-control" id="dtitle" name="dtitle" value="<?php echo htmlspecialchars($doc['dtitle']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="daccess">Access Level:</label>
                        <select class="form-control" id="daccess" name="daccess" required>
                            <option value="Private" <?php echo ($doc['daccess'] == 'Private') ? 'selected' : ''; ?>>Private</option>
                            <option value="Public" <?php echo ($doc['daccess'] == 'Public') ? 'selected' : ''; ?>>Public</option>
                            <option value="Department" <?php echo ($doc['daccess'] == 'Department') ? 'selected' : ''; ?>>Department</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ddept">Department:</label>
                        <select class="form-control" id="ddept" name="ddept" <?php echo ($doc['daccess'] != 'Department') ? 'disabled' : ''; ?>>
                            <option value="">Select Department</option>
                            <option value="Computer Science" <?php echo ($doc['ddept'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                            <option value="Math Science" <?php echo ($doc['ddept'] == 'Math Science') ? 'selected' : ''; ?>>Math Science</option>
                            <option value="Statistics" <?php echo ($doc['ddept'] == 'Statistics') ? 'selected' : ''; ?>>Statistics</option>
                            <option value="Natural Science" <?php echo ($doc['ddept'] == 'Natural Science') ? 'selected' : ''; ?>>Natural Science</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dcomment">Comments:</label>
                        <textarea class="form-control" id="dcomment" name="dcomment" rows="4"><?php echo htmlspecialchars($doc['dcomment']); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Document</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Your document has been updated successfully.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Enable or disable department dropdown based on access level
        document.getElementById('daccess').addEventListener('change', function() {
            var departmentSelect = document.getElementById('ddept');
            if (this.value === 'Department') {
                departmentSelect.disabled = false;
            } else {
                departmentSelect.disabled = true;
                departmentSelect.value = '';
            }
        });

        // Show success modal if update success session variable is set
        <?php if (isset($_SESSION['update_success']) && $_SESSION['update_success']): ?>
            $(document).ready(function() {
                $('#successModal').modal('show');
                setTimeout(function() {
                    window.location.href = 'download_document.php'; // Redirect after 1 second
                }, 2000); // 1 second delay
                <?php unset($_SESSION['update_success']); ?> // Clear the session variable
            });
        <?php endif; ?>
    </script>
</body>
</html>
