<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Fetch user data
$user = null;
$sql = "SELECT * FROM Users WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing user query: " . $conn->error);
}
$stmt->bind_param("i", $_SESSION['userid']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>Error: User not found.</div>";
    exit();
}

$stmt->close();

// Fetch documents based on access level
$documents = [];
$udept = $user['udept'] ?? '';
$userid = $_SESSION['userid'];

// SQL query to fetch documents based on their access level
$sql = "SELECT id, dname, dtitle, dsize1 AS dsize, dtype, dpath 
        FROM Document 
        WHERE (daccess = 'Public') 
           OR (daccess = 'Department' AND ddept = ?) 
           OR (daccess = 'User' AND id IN (SELECT dname FROM document WHERE dtitle = ?))";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing documents query: " . $conn->error);
}
$stmt->bind_param("si", $udept, $userid);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
} else {
    $documents = []; // No documents found
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="styles.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
        }
        .navbar-brand img {
            height: 40px;
            width: auto;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .card-title {
            font-size: 1.5rem;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .documents-table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <!-- Main content -->
    <div class="container">
        <!-- Search Bar -->
        <div class="row">
            <div class="col-md-12 search-bar">
                <form method="GET" action="search_documents.php">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search documents..." required>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Action Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Profile</h4>
                    </div>
                    <div class="card-body">
                        <a href="profile.php" class="btn btn-primary">View Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Upload</h4>
                    </div>
                    <div class="card-body">
                        <a href="upload_document.php" class="btn btn-primary">Upload Document</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Share</h4>
                    </div>
                    <div class="card-body">
                        <a href="share_document.php" class="btn btn-primary">Share Document</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit</h4>
                    </div>
                    <div class="card-body">
                        <a href="download_document.php" class="btn btn-primary">Edit Document</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Delete</h4>
                    </div>
                    <div class="card-body">
                        <a href="delete_document.php" class="btn btn-primary">Delete Document</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Recycle Bin</h4>
                    </div>
                    <div class="card-body">
                        <a href="recycle_bin.php" class="btn btn-primary">Recycle Bin</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Table -->
        <div class="row documents-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Documents in Your Department (<?php echo htmlspecialchars($udept); ?>)</h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($documents) > 0): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Size</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($doc['id']); ?></td>
                                        <td><?php echo htmlspecialchars($doc['dname']); ?></td>
                                        <td><?php echo htmlspecialchars($doc['dtitle']); ?></td>
                                        <td><?php echo htmlspecialchars($doc['dsize']); ?></td>
                                        <td>
                                            <?php
                                            $docType = strtolower(htmlspecialchars($doc['dtype']));
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
                                        <td>
                                            <a href="<?php echo htmlspecialchars($doc['dpath']); ?>" class="btn btn-primary btn-sm" download>Download</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No documents found for your department.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>