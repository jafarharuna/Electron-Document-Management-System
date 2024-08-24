<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Function to decrypt file paths
function decryptPath($encryptedPath) {
    // Replace with your decryption logic
    return $encryptedPath; // Modify this line to reflect your decryption logic
}

// Get the user ID from session
$userid = $_SESSION['userid'];

// Query to fetch documents accessible by the user
$sql = "SELECT id, dname, dtitle, dsize1 AS dsize, dtype, ddept, downer, daccess, ddate, dtime, dpath 
        FROM Document 
        WHERE downer = ? OR daccess = 'Public' OR (daccess = 'Department' AND ddept = ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

// Bind parameters
$ddept = isset($_SESSION['ddept']) ? $_SESSION['ddept'] : 'Unknown';
$stmt->bind_param("ss", $userid, $ddept);

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
    <title>Download Documents</title>
    <link href="styles.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .modal-body img {
            max-width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main content -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>My Documents</h3>
            </div>
            <div class="card-body">
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
                            <th>Access Level</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['dname']); ?></td>
                            <td><?php echo htmlspecialchars($row['dtitle']); ?></td>
                            <td><?php echo htmlspecialchars($row['dsize']); ?></td>
                            <td>
                                <?php
                                $docType = strtolower(htmlspecialchars($row['dtype']));
                                switch ($docType) {
                                    case 'pdf':
                                        echo '<i class="bi bi-file-pdf" title="PDF"></i> PDF';
                                        break;
                                    case 'docx':
                                    case 'doc':
                                        echo '<i class="bi bi-file-word" title="Word"></i> DOCX';
                                        break;
                                    case 'txt':
                                        echo '<i class="bi bi-file-text" title="Text"></i> TXT';
                                        break;
                                    case 'jpg':
                                    case 'jpeg':
                                        echo '<i class="bi bi-file-image" title="JPEG"></i> JPG/JPEG';
                                        break;
                                    case 'png':
                                        echo '<i class="bi bi-file-image" title="PNG"></i> PNG';
                                        break;
                                    default:
                                        echo '<i class="bi bi-file-earmark-text" title="Unknown"></i> Unknown';
                                        break;
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['ddept']); ?></td>
                            <td><?php echo htmlspecialchars($row['downer']); ?></td>
                            <td><?php echo htmlspecialchars($row['daccess']); ?></td>
                            <td><?php echo htmlspecialchars($row['ddate']); ?></td>
                            <td><?php echo htmlspecialchars($row['dtime']); ?></td>
                            <td>
                                <a href="edit_document.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                                <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal" 
                                        data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                        data-name="<?php echo htmlspecialchars($row['dname']); ?>" 
                                        data-title="<?php echo htmlspecialchars($row['dtitle']); ?>"
                                        data-size="<?php echo htmlspecialchars($row['dsize']); ?>" 
                                        data-type="<?php echo htmlspecialchars($row['dtype']); ?>"
                                        data-dept="<?php echo htmlspecialchars($row['ddept']); ?>" 
                                        data-owner="<?php echo htmlspecialchars($row['downer']); ?>"
                                        data-access="<?php echo htmlspecialchars($row['daccess']); ?>" 
                                        data-date="<?php echo htmlspecialchars($row['ddate']); ?>" 
                                        data-time="<?php echo htmlspecialchars($row['dtime']); ?>" 
                                        data-path="<?php echo htmlspecialchars($row['dpath']); ?>">View</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Document Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="docDetails">
                        <p><strong>Name:</strong> <span id="docName"></span></p>
                        <p><strong>Title:</strong> <span id="docTitle"></span></p>
                        <p><strong>Size:</strong> <span id="docSize"></span></p>
                        <p><strong>Type:</strong> <span id="docTypeIcon"></span> <span id="docTypeText"></span></p>
                        <p><strong>Department:</strong> <span id="docDept"></span></p>
                        <p><strong>Owner:</strong> <span id="docOwner"></span></p>
                        <p><strong>Access Level:</strong> <span id="docAccess"></span></p>
                        <p><strong>Date:</strong> <span id="docDate"></span></p>
                        <p><strong>Time:</strong> <span id="docTime"></span></p>
                        <p><strong>Document Preview:</strong></p>
                        <div id="docPreview"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $('#viewModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var title = button.data('title');
            var size = button.data('size');
            var type = button.data('type');
            var dept = button.data('dept');
            var owner = button.data('owner');
            var access = button.data('access');
            var date = button.data('date');
            var time = button.data('time');
            var path = button.data('path');

            var modal = $(this);
            modal.find('#docName').text(name);
            modal.find('#docTitle').text(title);
            modal.find('#docSize').text(size);

            var docTypeIcon = '';
            var docTypeText = type;
            switch (type.toLowerCase()) {
                case 'pdf':
                    docTypeIcon = '<i class="bi bi-file-pdf" title="PDF"></i>';
                    docTypeText = 'PDF';
                    break;
                case 'docx':
                case 'doc':
                    docTypeIcon = '<i class="bi bi-file-word" title="Word"></i>';
                    docTypeText = 'DOCX';
                    break;
                case 'txt':
                    docTypeIcon = '<i class="bi bi-file-text" title="Text"></i>';
                    docTypeText = 'TXT';
                    break;
                case 'jpg':
                case 'jpeg':
                    docTypeIcon = '<i class="bi bi-file-image" title="JPEG"></i>';
                    docTypeText = 'JPG/JPEG';
                    break;
                case 'png':
                    docTypeIcon = '<i class="bi bi-file-image" title="PNG"></i>';
                    docTypeText = 'PNG';
                    break;
                default:
                    docTypeIcon = '<i class="bi bi-file-earmark-text" title="Unknown"></i>';
                    docTypeText = 'Unknown';
                    break;
            }
            modal.find('#docTypeIcon').html(docTypeIcon);
            modal.find('#docTypeText').text(docTypeText);
            modal.find('#docDept').text(dept);
            modal.find('#docOwner').text(owner);
            modal.find('#docAccess').text(access);
            modal.find('#docDate').text(date);
            modal.find('#docTime').text(time);

            var decryptedPath = decryptPath(path); // Replace with your decryption function if needed
            var previewContainer = modal.find('#docPreview');

            if (type === 'jpg' || type === 'jpeg' || type === 'png' || type === 'gif') {
                previewContainer.html('<img src="' + decryptedPath + '" alt="Document Preview">');
            } else {
                previewContainer.html('<p>Preview not available for this type of document.</p>');
            }
        });

        // Example decryption function (modify as needed)
        function decryptPath(encryptedPath) {
            // Replace with actual decryption logic if necessary
            return encryptedPath;
        }
    </script>
</body>
</html>
