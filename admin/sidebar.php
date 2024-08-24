<style>
        .navbar-nav .nav-link {
            color: black; /* Make text color black */
        }
        .navbar-nav .nav-link:hover {
            color: #007bff; /* Optional: Change color on hover */
        }
        #leftsidebar {
            width: 250px;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: #f8f9fa; /* Adjust as needed */
            overflow-y: auto;
            z-index: 1000;
        }
        #leftsidebar .navbar-nav {
            flex-direction: column;
        }
        #leftsidebar .navbar-nav .nav-item {
            padding: 10px 15px;
        }
    </style>

<!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <div class="navbar-brand">
            <a href="index.php"><img src="assets/images/logo.svg" width="25" alt="Aero"><span class="m-l-10">Aero</span></a>
        </div>
        <div class="menu">
            <ul class="navbar-nav">
            <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">View My Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="download_document.php">View Document</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="delete_document.php">Delete Document</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="upload_document.php">Upload Document</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="share_document.php">Share Document</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="recycle_bin.php">Recycle Bin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="download_document.php">Edit Document</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="search_documents.php">Quick Search</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </aside>