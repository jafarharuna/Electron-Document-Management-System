<?php
session_start();
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Handle add, edit, and delete operations
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        // Add user
        $uname = $_POST['uname'];
        $upass = $_POST['upass'];
        $ulname = $_POST['ulname'];
        $uoname = $_POST['uoname'];
        $uphone = $_POST['uphone'];
        $ustatus = $_POST['ustatus'];
        $ulevel = $_POST['ulevel'];
        $udept = $_POST['udept'];
        $uemail = $_POST['uemail'];
        
        $sql = "INSERT INTO users (uname, upass, ulname, uoname, uphone, ustatus, ulevel, udept, uemail)
                VALUES ('$uname', '$upass', '$ulname', '$uoname', '$uphone', '$ustatus', '$ulevel', '$udept', '$uemail')";
        if ($conn->query($sql) === TRUE) {
            $message = "User added successfully.";
            $message_type = "success";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
            $message_type = "danger";
        }
    } elseif (isset($_POST['edit_user'])) {
        // Edit user
        $id = $_POST['id'];
        $uname = $_POST['uname'];
        $upass = $_POST['upass'];
        $ulname = $_POST['ulname'];
        $uoname = $_POST['uoname'];
        $uphone = $_POST['uphone'];
        $ustatus = $_POST['ustatus'];
        $ulevel = $_POST['ulevel'];
        $udept = $_POST['udept'];
        $uemail = $_POST['uemail'];
        
        $sql = "UPDATE users SET uname='$uname', upass='$upass', ulname='$ulname', uoname='$uoname', uphone='$uphone', ustatus='$ustatus', ulevel='$ulevel', udept='$udept', uemail='$uemail' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $message = "User updated successfully.";
            $message_type = "success";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
            $message_type = "danger";
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete user
        $id = $_POST['id'];
        
        $sql = "DELETE FROM users WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            $message = "User deleted successfully.";
            $message_type = "success";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
            $message_type = "danger";
        }
    }

    // Redirect to users.php with success message
    echo "<script>
        window.onload = function() {
            $('#successModal').modal('show');
            setTimeout(function() {
                window.location.href = 'users.php';
            }, 3000);
        }
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>:: Aero Bootstrap4 Admin :: Users</title>
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
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            font-size: 0.85rem; /* Reduce font size */
        }
        th, td {
            padding: 0.75rem; /* Adjust padding */
        }
        @media (max-width: 768px) {
            .table {
                font-size: 0.75rem; /* Further reduce font size on small screens */
            }
            th, td {
                padding: 0.5rem; /* Further adjust padding on small screens */
            }
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
                <h3 class="card-title">Users Management</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Level</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM users";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>{$row['uname']}</td>
                                            <td>{$row['upass']}</td>
                                            <td>{$row['ulname']}</td>
                                            <td>{$row['uoname']}</td>
                                            <td>{$row['uphone']}</td>
                                            <td>{$row['ustatus']}</td>
                                            <td>{$row['ulevel']}</td>
                                            <td>{$row['udept']}</td>
                                            <td>{$row['uemail']}</td>
                                            <td>
                                                <form method='post' style='display:inline-block;'>
                                                    <input type='hidden' name='id' value='{$row['id']}'>
                                                    <button type='submit' name='delete_user' class='btn btn-danger btn-sm'>Delete</button>
                                                </form>
                                                <button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#editModal{$row['id']}'>Edit</button>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='11'>No records found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Add User Modal -->
                <button class="btn btn-success" data-toggle="modal" data-target="#addModal">Add User</button>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="add_uname">Username</label>
                            <input type="text" class="form-control" id="add_uname" name="uname" required>
                        </div>
                        <div class="form-group">
                            <label for="add_upass">Password</label>
                            <input type="password" class="form-control" id="add_upass" name="upass" required>
                        </div>
                        <div class="form-group">
                            <label for="add_ulname">First Name</label>
                            <input type="text" class="form-control" id="add_ulname" name="ulname">
                        </div>
                        <div class="form-group">
                            <label for="add_uoname">Last Name</label>
                            <input type="text" class="form-control" id="add_uoname" name="uoname">
                        </div>
                        <div class="form-group">
                            <label for="add_uphone">Phone</label>
                            <input type="text" class="form-control" id="add_uphone" name="uphone">
                        </div>
                        <div class="form-group">
                            <label for="add_ustatus">Status</label>
                            <input type="text" class="form-control" id="add_ustatus" name="ustatus">
                        </div>
                        <div class="form-group">
                            <label for="add_ulevel">Level</label>
                            <input type="text" class="form-control" id="add_ulevel" name="ulevel">
                        </div>
                        <div class="form-group">
                            <label for="add_udept">Department</label>
                            <select class="form-control" id="add_udept" name="udept">
                                <option value="Computer Science">Computer Science</option>
                                <option value="Maths Science">Maths Science</option>
                                <option value="Statistics">Statistics</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_uemail">Email</label>
                            <input type="email" class="form-control" id="add_uemail" name="uemail">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    // Edit User Modals
    $result->data_seek(0); // Reset result pointer
    while($row = $result->fetch_assoc()) {
        echo "<!-- Edit User Modal {$row['id']} -->
        <div class='modal fade' id='editModal{$row['id']}' tabindex='-1' role='dialog' aria-labelledby='editModalLabel{$row['id']}' aria-hidden='true'>
            <div class='modal-dialog' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='editModalLabel{$row['id']}'>Edit User</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <form method='post'>
                        <div class='modal-body'>
                            <div class='form-group'>
                                <label for='edit_uname{$row['id']}'>Username</label>
                                <input type='text' class='form-control' id='edit_uname{$row['id']}' name='uname' value='{$row['uname']}' required>
                            </div>
                            <div class='form-group'>
                                <label for='edit_upass{$row['id']}'>Password</label>
                                <input type='password' class='form-control' id='edit_upass{$row['id']}' name='upass' value='{$row['upass']}' required>
                            </div>
                            <div class='form-group'>
                                <label for='edit_ulname{$row['id']}'>First Name</label>
                                <input type='text' class='form-control' id='edit_ulname{$row['id']}' name='ulname' value='{$row['ulname']}'>
                            </div>
                            <div class='form-group'>
                                <label for='edit_uoname{$row['id']}'>Last Name</label>
                                <input type='text' class='form-control' id='edit_uoname{$row['id']}' name='uoname' value='{$row['uoname']}'>
                            </div>
                            <div class='form-group'>
                                <label for='edit_uphone{$row['id']}'>Phone</label>
                                <input type='text' class='form-control' id='edit_uphone{$row['id']}' name='uphone' value='{$row['uphone']}'>
                            </div>
                            <div class='form-group'>
                                <label for='edit_ustatus{$row['id']}'>Status</label>
                                <input type='text' class='form-control' id='edit_ustatus{$row['id']}' name='ustatus' value='{$row['ustatus']}'>
                            </div>
                            <div class='form-group'>
                                <label for='edit_ulevel{$row['id']}'>Level</label>
                                <input type='text' class='form-control' id='edit_ulevel{$row['id']}' name='ulevel' value='{$row['ulevel']}'>
                            </div>
                            <div class='form-group'>
                                <label for='edit_udept{$row['id']}'>Department</label>
                                <select class='form-control' id='edit_udept{$row['id']}' name='udept'>
                                    <option value='Computer Science' " . ($row['udept'] == 'Computer Science' ? 'selected' : '') . ">Computer Science</option>
                                    <option value='Maths Science' " . ($row['udept'] == 'Maths Science' ? 'selected' : '') . ">Maths Science</option>
                                    <option value='Statistics' " . ($row['udept'] == 'Statistics' ? 'selected' : '') . ">Statistics</option>
                                </select>
                            </div>
                            <div class='form-group'>
                                <label for='edit_uemail{$row['id']}'>Email</label>
                                <input type='email' class='form-control' id='edit_uemail{$row['id']}' name='uemail' value='{$row['uemail']}'>
                            </div>
                            <input type='hidden' name='id' value='{$row['id']}'>
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
                            <button type='submit' name='edit_user' class='btn btn-primary'>Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>";
    }
    ?>

    <!-- Success Message Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if ($message_type === 'success'): ?>
                        <div class="alert alert-success">
                            <?php echo $message; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
