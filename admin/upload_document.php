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
                // On successful upload, set a session variable to show the success modal
                $_SESSION['upload_success'] = true;
                $stmt->close();
                $conn->close();
                header("Location: upload_document.php");
                exit();
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

    // Decrypt file name
        
}
    
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
        .drop-zone {
            border: 2px dashed #007bff;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            color: #007bff;
            cursor: pointer;
        }
        .drop-zone.dragover {
            background-color: #e9ecef;
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
                <h3>Upload Document</h3>
            </div>
            <div class="card-body">
                <form id="uploadForm" action="upload_document.php" method="POST" enctype="multipart/form-data">
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
                        <label for="fileToUpload">Drag & Drop file here or click to select:</label>
                        <div class="drop-zone" id="dropZone">Drop files here</div>
                        <input type="file" id="fileInput" name="fileToUpload" style="display: none;">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
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
                    <p>Your document has been uploaded successfully.</p>
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
        function toggleDeptDropdown() {
            const accessLevel = document.getElementById('daccess').value;
            const deptDropdown = document.getElementById('deptDropdown');
            deptDropdown.style.display = accessLevel === 'Department' ? 'block' : 'none';
        }

        document.getElementById('dropZone').addEventListener('click', function() {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function() {
            document.getElementById('dropZone').textContent = this.files[0].name;
        });

        document.getElementById('dropZone').addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('dragover');
        });

        document.getElementById('dropZone').addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
        });

        document.getElementById('dropZone').addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('fileInput').files = files;
                document.getElementById('dropZone').textContent = files[0].name;
            }
        });

        // Initialize dropdown state
        toggleDeptDropdown();

        // Show success modal if upload success session variable is set
        <?php if (isset($_SESSION['upload_success']) && $_SESSION['upload_success']): ?>
            $(document).ready(function() {
                $('#successModal').modal('show');
                setTimeout(function() {
                    window.location.href = 'download_document.php';
                }, 3000); // Redirect after 1 second
                <?php unset($_SESSION['upload_success']); ?> // Clear the session variable
            });
        <?php endif; ?>
    </script>
</body>
</html>