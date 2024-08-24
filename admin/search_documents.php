<?php
include 'config.php';

// Get the search query from the GET request
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Prevent SQL injection
$search = $conn->real_escape_string($search);

// SQL query to fetch documents based on the search query
$sql = "SELECT id, dname, dtitle, dsize1 AS dsize, dtype, dpath
        FROM Document
        WHERE dname LIKE ? OR dtitle LIKE ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing SQL statement: " . $conn->error);
}

// Use %wildcard% for partial matches
$searchParam = '%' . $search . '%';
$stmt->bind_param('ss', $searchParam, $searchParam);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo '<table class="table table-striped">';
        echo '<thead><tr><th>ID</th><th>Name</th><th>Title</th><th>Size</th><th>Type</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['dname']) . '</td>';
            echo '<td>' . htmlspecialchars($row['dtitle']) . '</td>';
            echo '<td>' . htmlspecialchars($row['dsize']) . '</td>';
            echo '<td>' . htmlspecialchars($row['dtype']) . '</td>';
            echo '<td><a href="' . htmlspecialchars($row['dpath']) . '" class="btn btn-primary btn-sm" download>Download</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No documents found.</p>';
    }
} else {
    echo '<p>Error executing SQL query.</p>';
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Documents</title>
    <link href="styles.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .search-bar {
            margin-bottom: 20px;
        }
        .documents-table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'sidebar.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12 search-bar">
                <input type="text" id="searchInput" class="form-control" placeholder="Search documents..." onkeyup="searchDocuments()">
            </div>
        </div>

        <!-- Documents Table -->
        <div class="row documents-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Documents</h4>
                    </div>
                    <div class="card-body">
                        <div id="results">
                            <!-- Results will be loaded here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function searchDocuments() {
            const searchValue = document.getElementById('searchInput').value;
            const resultsDiv = document.getElementById('results');

            // Check if search value is empty
            if (searchValue.trim() === '') {
                resultsDiv.innerHTML = ''; // Clear results if input is empty
                return;
            }

            // Create an XMLHttpRequest object
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'search_documents.php?search=' + encodeURIComponent(searchValue), true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        resultsDiv.innerHTML = xhr.responseText;
                    } else {
                        resultsDiv.innerHTML = '<p>Error fetching search results.</p>';
                    }
                }
            };
            xhr.send();
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
