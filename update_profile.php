<?php
// update_profile.php

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];

// Handle Basic Information Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_basic') {
    $name = trim($_POST['name']);
    $title = trim($_POST['title']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $github = trim($_POST['github']);
    $location = trim($_POST['location']);
    $about_me = trim($_POST['about_me']);

    // Validation
    if (empty($name) || empty($email)) {
        $_SESSION['message'] = 'Name and Email are required!';
        $_SESSION['message_type'] = 'error';
    } else {
        try {
            // Check if personal_info record exists
            $stmt = $pdo->prepare("SELECT piid FROM personal_info WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $exists = $stmt->fetch();

            if ($exists) {
                // Update existing record
                $stmt = $pdo->prepare("UPDATE personal_info 
                    SET name = ?, title = ?, phone = ?, email = ?, github_link = ?, address_loc = ?, about_me = ? 
                    WHERE user_id = ?");
                $stmt->execute([$name, $title, $phone, $email, $github, $location, $about_me, $user_id]);
            } else {
                // Insert new record if it doesn't exist
                $stmt = $pdo->prepare("INSERT INTO personal_info (user_id, name, title, phone, email, github_link, address_loc, about_me) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $name, $title, $phone, $email, $github, $location, $about_me]);
            }

            $_SESSION['message'] = 'Personal information updated successfully! ✅';
            $_SESSION['message_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Error updating information: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
    }

    header('Location: dashboard.php');
    exit();
}

// If no valid action, redirect back
header('Location: dashboard.php');
exit();
