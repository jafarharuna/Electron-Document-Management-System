<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Ensure session variables are set
$uname = isset($_SESSION['uname']) ? $_SESSION['uname'] : 'Document';
$ddept = isset($_SESSION['ddept']) ? $_SESSION['ddept'] : 'Unknown';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dtitle = $_POST['dtitle'];
    $dcomment = $_POST['dcomment'];
    $daccess = $_POST['daccess'];
    $ddate = date("Y-m-d");
    $dtime = date("H:i:s");
    $downer = $_SESSION['userid'];

    // Check if department is selected
    if ($daccess === 'Department') {
        $ddept = $_POST['ddept']; // Get the selected department
    }

    // Handle file upload
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['fileToUpload']['tmp_name'];
        $fileName = $_FILES['fileToUpload']['name'];
        $dsize1 = $_FILES['fileToUpload']['size'];
        $fileType = $_FILES['fileToUpload']['type'];
        $dsize2 = $dsize1; // Assuming size2 is the same as size1 for this example

        // Define the file path and folder
        $uploadFolder = 'document_uploads/';
        $dpath = $uploadFolder . $fileName;

        // Encrypt file name
        $dencrypt = md5($fileName . time());

        // Move the file to the server directory
        if (move_uploaded_file($fileTmpPath, $dpath)) {
            // Extract file extension
            $dtype = pathinfo($fileName, PATHINFO_EXTENSION);

            // Insert document info into the database
            $sql = "INSERT INTO Document 
                    (dname, dtitle, downer, dsize1, dsize2, dtype, daccess, ddate, dtime, ddept, ddept2, dstatus, dencrypt, dpath, deditdate, dedittime, dcomment) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            if ($stmt === false) {
                die("Error preparing the statement: " . $conn->error);
            }

            // Bind parameters
            $stmt->bind_param(
                "ssissssssssssssss",
                $uname, $dtitle, $downer, $dsize1, $dsize2, $dtype, $daccess, $ddate, $dtime, $ddept, $ddept2, $dstatus, $dencrypt, $dpath, $ddate, $dtime, $dcomment
            );

            if ($stmt->execute()) {
                echo "Document uploaded successfully.";
            } else {
                echo "Error executing the statement: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file uploaded or error in uploading.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Document</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
    <script>
        function toggleDeptDropdown() {
            const accessLevel = document.getElementById('daccess').value;
            const deptDropdown = document.getElementById('deptDropdown');
            deptDropdown.style.display = accessLevel === 'Department' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main content -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Upload Document</h3>
            </div>
            <div class="card-body">
                <form action="upload_document.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="dtitle">Document Title:</label>
                        <input type="text" class="form-control" id="dtitle" name="dtitle" required>
                    </div>
                    <div class="form-group">
                        <label for="dcomment">Comments:</label>
                        <textarea class="form-control" id="dcomment" name="dcomment" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="daccess">Access Level:</label>
                        <select class="form-control" id="daccess" name="daccess" required onchange="toggleDeptDropdown()">
                            <option value="Private">Private</option>
                            <option value="Public">Public</option>
                            <option value="Department">Department</option>
                        </select>
                    </div>
                    <div class="form-group" id="deptDropdown" style="display: none;">
                        <label for="ddept">Select Department:</label>
                        <select class="form-control" id="ddept" name="ddept">
                            <option value="Computer Science">Computer Science</option>
                            <option value="Math Science">Math Science</option>
                            <option value="Statistics">Statistics</option>
                            <option value="Natural Science">Natural Science</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fileToUpload">Select file to upload:</label>
                        <input type="file" class="form-control-file" id="fileToUpload" name="fileToUpload" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
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