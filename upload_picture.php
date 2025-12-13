<?php
// upload_picture.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id'];
    $pdo = getDBConnection();

    $file = $_FILES['profile_pic'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = 'Upload failed. Error code: ' . $file['error'];
        $_SESSION['message_type'] = 'error';
        header('Location: dashboard.php');
        exit();
    }

    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validate file type
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['message'] = '❌ Invalid file type. Please upload JPG, PNG, GIF, or WebP.';
        $_SESSION['message_type'] = 'error';
        header('Location: dashboard.php');
        exit();
    }

    // Validate file size
    if ($file['size'] > $max_size) {
        $_SESSION['message'] = '❌ File too large. Maximum size is 5MB.';
        $_SESSION['message_type'] = 'error';
        header('Location: dashboard.php');
        exit();
    }

    // Create upload directory if it doesn't exist
    $upload_dir = 'assets/profile_pics/' . $user_id . '/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $_SESSION['message'] = '❌ Failed to create upload directory.';
            $_SESSION['message_type'] = 'error';
            header('Location: dashboard.php');
            exit();
        }
    }

    // Delete old profile picture if exists
    $stmt = $pdo->prepare("SELECT profile_pic_path FROM personal_info WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $old_pic = $stmt->fetchColumn();

    if ($old_pic && file_exists($old_pic)) {
        unlink($old_pic);
    }

    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $file_path = $upload_dir . $filename;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Check if personal_info record exists
        $stmt = $pdo->prepare("SELECT piid FROM personal_info WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update existing record
            $stmt = $pdo->prepare("UPDATE personal_info SET profile_pic_path = ? WHERE user_id = ?");
            $stmt->execute([$file_path, $user_id]);
        } else {
            // Insert new record if it doesn't exist
            $stmt = $pdo->prepare("INSERT INTO personal_info (user_id, profile_pic_path, name, email) VALUES (?, ?, 'New User', 'user@example.com')");
            $stmt->execute([$user_id, $file_path]);
        }

        $_SESSION['message'] = '✅ Profile picture updated successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = '❌ Failed to upload file. Please check folder permissions.';
        $_SESSION['message_type'] = 'error';
    }

    header('Location: dashboard.php');
    exit();
} else {
    $_SESSION['message'] = '❌ No file uploaded.';
    $_SESSION['message_type'] = 'error';
    header('Location: dashboard.php');
    exit();
}
