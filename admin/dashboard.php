<?php
session_start();
if (!isset($_SESSION['userid'])) {
    die("Session userid not set.");
}

// Debugging session value
echo "<div class='alert alert-info'>Session userid: " . htmlspecialchars($_SESSION['userid']) . "</div>";

include 'config.php'; // Ensure $conn is correctly initialized

// Fetch user data
$user = null;
$userid = $_SESSION['userid'];
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing user query: " . $conn->error);
}

$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     $user = $result->fetch_assoc();
//     echo "<div class='alert alert-info'>User found: " . htmlspecialchars(print_r($user, true)) . "</div>";
// } else {
//     echo "<div class='alert alert-danger'>Error: User not found. ID: " . htmlspecialchars($userid) . "</div>";
// }
// $stmt->close();

// Fetch all documents
$documents = [];
$sql = "SELECT id, dname, dtitle, dsize1 AS dsize, dtype, dpath FROM Document";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
}
?>

<!doctype html>
<html class="no-js" lang="en">
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
    <link rel="stylesheet" href="assets/css/style.min.css">
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
<body class="theme-blush">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="m-t-30"><img class="zmdi-hc-spin" src="assets/images/loader.svg" width="48" height="48" alt="Aero"></div>
            <p>Please wait...</p>
        </div>
    </div>

    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>

    <!-- Main Search -->
    <div id="search">
        <button id="close" type="button" class="close btn btn-primary btn-icon btn-icon-mini btn-round">x</button>
        <form>
            <input type="search" value="" placeholder="Search..." />
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <!-- Left Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <section class="content">
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
            <!-- Action Cards as before -->
        </div>

        <!-- Documents Table -->
        <div class="row documents-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Documents</h4>
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
                                            <!-- View Button -->
                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal" data-id="<?php echo htmlspecialchars($doc['id']); ?>" data-name="<?php echo htmlspecialchars($doc['dname']); ?>" data-title="<?php echo htmlspecialchars($doc['dtitle']); ?>" data-size="<?php echo htmlspecialchars($doc['dsize']); ?>" data-type="<?php echo htmlspecialchars($doc['dtype']); ?>" data-path="<?php echo htmlspecialchars($doc['dpath']); ?>">View</button>
                                            <!-- Edit Button -->
                                           
                                            <!-- Delete Button -->
                                            <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo htmlspecialchars($doc['id']); ?>">Delete</button>
                                            <!-- Download Button -->
                                            <a href="<?php echo htmlspecialchars($doc['dpath']); ?>" class="btn btn-primary btn-sm" download>Download</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No documents available in the database.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">View Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>ID:</strong> <span id="view-id"></span></p>
                    <p><strong>Name:</strong> <span id="view-name"></span></p>
                    <p><strong>Title:</strong> <span id="view-title"></span></p>
                    <p><strong>Size:</strong> <span id="view-size"></span></p>
                    <p><strong>Type:</strong> <span id="view-type"></span></p>
                    <p><a id="view-link" href="" target="_blank">View Document</a></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Document</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="delete-form" method="POST" action="delete_document.php">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="delete-id">
                        <p>Are you sure you want to delete this document?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/bundles/libscripts.bundle.js"></script>
    <script src="assets/bundles/vendorscripts.bundle.js"></script>
    <script src="assets/bundles/jvectormap.bundle.js"></script>
    <script src="assets/bundles/sparkline.bundle.js"></script>
    <script src="assets/bundles/c3.bundle.js"></script>
    <script src="assets/bundles/mainscripts.bundle.js"></script>
    <script src="assets/js/pages/index.js"></script>

    <script>
        // Handle view modal
        $('#viewModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id');
            var name = button.data('name');
            var title = button.data('title');
            var size = button.data('size');
            var type = button.data('type');
            var path = button.data('path');

            var modal = $(this);
            modal.find('#view-id').text(id);
            modal.find('#view-name').text(name);
            modal.find('#view-title').text(title);
            modal.find('#view-size').text(size);
            modal.find('#view-type').text(type);
            modal.find('#view-link').attr('href', path);
        });

        // Handle edit modal
        // $('#editModal').on('show.bs.modal', function (event) {
        //     var button = $(event.relatedTarget); // Button that triggered the modal
        //     var id = button.data('id');
        //     var name = button.data('name');
        //     var title = button.data('title');

        //     var modal = $(this);
        //     modal.find('#edit-id').val(id);
        //     modal.find('#edit-name').val(name);
        //     modal.find('#edit-title').val(title);
        // });

        // Handle delete modal
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id');

            var modal = $(this);
            modal.find('#delete-id').val(id);
        });
    </script>
</body>
</html>
