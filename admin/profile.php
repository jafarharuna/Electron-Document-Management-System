<?php
session_start();  // Ensure this is at the very top of the file

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

$userid = $_SESSION['userid'];

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_username') {
        $uname = $_POST['uname'] ?? '';
        $sql = "UPDATE admins SET uname = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uname, $userid);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Username updated successfully.'); setTimeout(function() { window.location.href = 'profile.php'; }, 3000);</script>";
    } elseif ($action === 'update_email') {
        $uemail = $_POST['uemail'] ?? '';
        $sql = "UPDATE admins SET uemail = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uemail, $userid);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Email updated successfully.'); setTimeout(function() { window.location.href = 'profile.php'; }, 3000);</script>";
    } elseif ($action === 'update_phone') {
        $uphone = $_POST['uphone'] ?? '';
        $sql = "UPDATE admins SET uphone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uphone, $userid);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Phone number updated successfully.'); setTimeout(function() { window.location.href = 'profile.php'; }, 3000);</script>";
    } elseif ($action === 'update_dept') {
        $udept = $_POST['udept'] ?? '';
        $sql = "UPDATE admins SET udept = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $udept, $userid);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Department updated successfully.'); setTimeout(function() { window.location.href = 'profile.php'; }, 3000);</script>";
    } elseif ($action === 'update_level') {
        $ulevel = $_POST['ulevel'] ?? '';
        $sql = "UPDATE admins SET ulevel = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $ulevel, $userid);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('User level updated successfully.'); setTimeout(function() { window.location.href = 'profile.php'; }, 3000);</script>";
    } elseif ($action === 'update_password') {
        $current_pass = $_POST['current_pass'] ?? '';
        $new_pass = $_POST['new_pass'] ?? '';

        // Verify current password
        $sql = "SELECT upass FROM admins WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($current_pass, $user['upass'])) {
            // Update to new password
            $new_pass_hash = password_hash($new_pass, PASSWORD_BCRYPT);
            $sql = "UPDATE admins SET upass = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_pass_hash, $userid);
            $stmt->execute();
            $stmt->close();
            echo "<script>alert('Password updated successfully.'); setTimeout(function() { window.location.href = 'profile.php'; }, 3000);</script>";
        } else {
            echo "<script>alert('Current password is incorrect.');</script>";
        }
    }

    exit();
}

// Fetch user details
$sql = "SELECT uname, uemail, uphone, udept, ulevel FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Check if user data was retrieved successfully
if (!$user) {
    die("Error: User not found. User ID: $userid");
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
        .form-control {
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'sidebar.php'; ?>
    <section class="content">
        <!-- Main content -->
        <div class="container">
            <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
            <div class="card">
                <div class="card-header">
                    <h3>User Profile</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="uname">Username</label>
                            <div class="input-group">
                                <input type="text" id="uname" name="uname" class="form-control" value="<?php echo htmlspecialchars($user['uname']); ?>" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editUsernameModal">Edit</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="uemail">Email</label>
                            <div class="input-group">
                                <input type="email" id="uemail" name="uemail" class="form-control" value="<?php echo htmlspecialchars($user['uemail']); ?>" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editEmailModal">Edit</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="uphone">Phone Number</label>
                            <div class="input-group">
                                <input type="text" id="uphone" name="uphone" class="form-control" value="<?php echo htmlspecialchars($user['uphone']); ?>" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPhoneModal">Edit</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="udept">Department</label>
                            <div class="input-group">
                                <input type="text" id="udept" name="udept" class="form-control" value="<?php echo htmlspecialchars($user['udept']); ?>" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editDeptModal">Edit</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ulevel">User Level</label>
                            <div class="input-group">
                                <input type="text" id="ulevel" name="ulevel" class="form-control" value="<?php echo htmlspecialchars($user['ulevel']); ?>" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editLevelModal">Edit</button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="upass">Password</label>
                            <div class="input-group">
                                <input type="password" id="upass" name="upass" class="form-control" placeholder="Enter your current password" readonly>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPasswordModal">Change</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Username Modal -->
        <div class="modal fade" id="editUsernameModal" tabindex="-1" role="dialog" aria-labelledby="editUsernameModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUsernameModalLabel">Edit Username</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_uname">New Username</label>
                                <input type="text" id="edit_uname" name="uname" class="form-control" value="<?php echo htmlspecialchars($user['uname']); ?>" required>
                                <input type="hidden" name="action" value="update_username">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Email Modal -->
        <div class="modal fade" id="editEmailModal" tabindex="-1" role="dialog" aria-labelledby="editEmailModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editEmailModalLabel">Edit Email</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_uemail">New Email</label>
                                <input type="email" id="edit_uemail" name="uemail" class="form-control" value="<?php echo htmlspecialchars($user['uemail']); ?>" required>
                                <input type="hidden" name="action" value="update_email">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Phone Modal -->
        <div class="modal fade" id="editPhoneModal" tabindex="-1" role="dialog" aria-labelledby="editPhoneModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPhoneModalLabel">Edit Phone Number</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_uphone">New Phone Number</label>
                                <input type="text" id="edit_uphone" name="uphone" class="form-control" value="<?php echo htmlspecialchars($user['uphone']); ?>" required>
                                <input type="hidden" name="action" value="update_phone">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Department Modal -->
        <div class="modal fade" id="editDeptModal" tabindex="-1" role="dialog" aria-labelledby="editDeptModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editDeptModalLabel">Edit Department</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_udept">New Department</label>
                                <select id="edit_udept" name="udept" class="form-control" required>
                                    <option value="Computer Science" <?php echo ($user['udept'] === 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                                    <option value="Maths Science" <?php echo ($user['udept'] === 'Maths Science') ? 'selected' : ''; ?>>Maths Science</option>
                                    <option value="Statistics" <?php echo ($user['udept'] === 'Statistics') ? 'selected' : ''; ?>>Statistics</option>
                                </select>
                                <input type="hidden" name="action" value="update_dept">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Level Modal -->
        <div class="modal fade" id="editLevelModal" tabindex="-1" role="dialog" aria-labelledby="editLevelModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editLevelModalLabel">Edit User Level</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="edit_ulevel">New User Level</label>
                                <input type="text" id="edit_ulevel" name="ulevel" class="form-control" value="<?php echo htmlspecialchars($user['ulevel']); ?>" required>
                                <input type="hidden" name="action" value="update_level">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Password Modal -->
        <div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPasswordModalLabel">Change Password</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="current_pass">Current Password</label>
                                <input type="password" id="current_pass" name="current_pass" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="new_pass">New Password</label>
                                <input type="password" id="new_pass" name="new_pass" class="form-control" required>
                            </div>
                            <input type="hidden" name="action" value="update_password">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Include JS Files -->
    <script src="assets/plugins/jquery/jquery.min.js"></script>
    <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="assets/plugins/charts-c3/plugin.js"></script>
    <script src="assets/plugins/morrisjs/morris.min.js"></script>
    <script src="assets/js/core.js"></script>
</body>
</html>
