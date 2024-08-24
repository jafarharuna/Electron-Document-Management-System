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

// Check if the file parameter is set
if (isset($_GET['file'])) {
    $filePath = decryptPath($_GET['file']);

    // Validate the file path and ensure it exists
    if (file_exists($filePath)) {
        // Force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush(); // Flush system output buffer
        readfile($filePath);
        exit;
    } else {
        die("Error: File not found.");
    }
} else {
    die("Error: No file specified.");
}
?>
