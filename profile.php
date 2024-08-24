<?php
session_start();
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
        $sql = "UPDATE Users SET uname = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uname, $userid);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'update_email') {
        $uemail = $_POST['uemail'] ?? '';
        $sql = "UPDATE Users SET uemail = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uemail, $userid);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'update_phone') {
        $uphone = $_POST['uphone'] ?? '';
        $sql = "UPDATE Users SET uphone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $uphone, $userid);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'update_dept') {
        $udept = $_POST['udept'] ?? '';
        $sql = "UPDATE Users SET udept = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $udept, $userid);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'update_level') {
        $ulevel = $_POST['ulevel'] ?? '';
        $sql = "UPDATE Users SET ulevel = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $ulevel, $userid);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'update_password') {
        $current_pass = $_POST['current_pass'] ?? '';
        $new_pass = $_POST['new_pass'] ?? '';
        
        // Verify current password
        $sql = "SELECT upass FROM Users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if (password_verify($current_pass, $user['upass'])) {
            // Update to new password
            $new_pass_hash = password_hash($new_pass, PASSWORD_BCRYPT);
            $sql = "UPDATE Users SET upass = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_pass_hash, $userid);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "<script>alert('Current password is incorrect.');</script>";
        }
    }

    // Redirect to avoid form resubmission
    header("Location: profile.php");
    exit();
}

// Fetch user details
$sql = "SELECT uname, uemail, uphone, udept, ulevel FROM Users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editUsernameModalLabel">Edit Username</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_uname">New Username</label>
                            <input type="text" id="edit_uname" name="uname" class="form-control" value="<?php echo htmlspecialchars($user['uname']); ?>">
                            <input type="hidden" name="action" value="update_username">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmailModalLabel">Edit Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_uemail">New Email</label>
                            <input type="email" id="edit_uemail" name="uemail" class="form-control" value="<?php echo htmlspecialchars($user['uemail']); ?>">
                            <input type="hidden" name="action" value="update_email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editPhoneModalLabel">Edit Phone Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_uphone">New Phone Number</label>
                            <input type="text" id="edit_uphone" name="uphone" class="form-control" value="<?php echo htmlspecialchars($user['uphone']); ?>">
                            <input type="hidden" name="action" value="update_phone">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editDeptModalLabel">Edit Department</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_udept">New Department</label>
                            <select id="edit_udept" name="udept" class="form-control">
                                <option value="Computer Science" <?php echo ($user['udept'] === 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                                <option value="Math Science" <?php echo ($user['udept'] === 'Math Science') ? 'selected' : ''; ?>>Math Science</option>
                                <option value="Statistics" <?php echo ($user['udept'] === 'Statistics') ? 'selected' : ''; ?>>Statistics</option>
                            </select>
                            <input type="hidden" name="action" value="update_dept">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editLevelModalLabel">Edit User Level</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_ulevel">New User Level</label>
                            <input type="text" id="edit_ulevel" name="ulevel" class="form-control" value="<?php echo htmlspecialchars($user['ulevel']); ?>">
                            <input type="hidden" name="action" value="update_level">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="editPasswordModal" tabindex="-1" role="dialog" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="current_pass">Current Password</label>
                            <input type="password" id="current_pass" name="current_pass" class="form-control" placeholder="Enter your current password">
                        </div>
                        <div class="form-group">
                            <label for="new_pass">New Password</label>
                            <input type="password" id="new_pass" name="new_pass" class="form-control" placeholder="Enter new password">
                        </div>
                        <input type="hidden" name="action" value="update_password">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
