<?php
// update_handler.php

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        // ============ SKILLS ============
        case 'add_skill':
            $skill_name = trim($_POST['skill_name']);
            if (!empty($skill_name)) {
                $stmt = $pdo->prepare("INSERT INTO skills (user_id, skill_name) VALUES (?, ?)");
                $stmt->execute([$user_id, $skill_name]);
                $_SESSION['message'] = ' ✅ Skill added successfully!';
                $_SESSION['message_type'] = 'success';
            }
            break;

        case 'delete_skill':
            $skill_id = $_POST['skill_id'];
            $stmt = $pdo->prepare("DELETE FROM skills WHERE skid = ? AND user_id = ?");
            $stmt->execute([$skill_id, $user_id]);
            $_SESSION['message'] = ' 🗑️ Skill deleted successfully!';
            $_SESSION['message_type'] = 'success';
            break;

        // ============ EDUCATION ============
        case 'add_education':
            $degree = trim($_POST['degree']);
            $school = trim($_POST['school']);
            $year = trim($_POST['year']);
            $location = trim($_POST['location']);

            if (!empty($degree) && !empty($school)) {
                $stmt = $pdo->prepare("INSERT INTO education (user_id, degree, school, year, location) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $degree, $school, $year, $location]);
                $_SESSION['message'] = 'Education added successfully! 🎓';
                $_SESSION['message_type'] = 'success';
            }
            break;

        case 'edit_education':
            $edu_id = $_POST['edu_id'];
            $degree = trim($_POST['degree']);
            $school = trim($_POST['school']);
            $year = trim($_POST['year']);
            $location = trim($_POST['location']);

            $stmt = $pdo->prepare("UPDATE education SET degree = ?, school = ?, year = ?, location = ? WHERE eid = ? AND user_id = ?");
            $stmt->execute([$degree, $school, $year, $location, $edu_id, $user_id]);
            $_SESSION['message'] = ' ✅ Education updated successfully!';
            $_SESSION['message_type'] = 'success';
            break;

        case 'delete_education':
            $edu_id = $_POST['edu_id'];
            $stmt = $pdo->prepare("DELETE FROM education WHERE eid = ? AND user_id = ?");
            $stmt->execute([$edu_id, $user_id]);
            $_SESSION['message'] = ' 🗑️ Education deleted successfully!';
            $_SESSION['message_type'] = 'success';
            break;

        // ============ PROJECTS ============
        case 'add_project':
            $title = trim($_POST['title']);
            $project_type = trim($_POST['project_type']);
            $project_date = trim($_POST['project_date']);
            $link = trim($_POST['link']);
            $description = trim($_POST['description']);
            $features = $_POST['features'] ?? [];

            if (!empty($title)) {
                // Insert project first
                $stmt = $pdo->prepare("INSERT INTO projects (user_id, title, project_type, project_date, link, description) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $title, $project_type, $project_date, $link, $description]);

                // Get the inserted project ID
                $project_id = $pdo->lastInsertId();

                // Insert features into project_features table
                $features_filtered = array_filter($features);
                if (!empty($features_filtered)) {
                    $stmt = $pdo->prepare("INSERT INTO project_features (project_id, feature_desc) VALUES (?, ?)");
                    foreach ($features_filtered as $feature) {
                        $stmt->execute([$project_id, trim($feature)]);
                    }
                }

                $_SESSION['message'] = ' 🚀 Project added successfully!';
                $_SESSION['message_type'] = 'success';
            }
            break;

        case 'edit_project':
            $project_id = $_POST['project_id'];
            $title = trim($_POST['title']);
            $project_type = trim($_POST['project_type']);
            $project_date = trim($_POST['project_date']);
            $link = trim($_POST['link']);
            $description = trim($_POST['description']);
            $features = $_POST['features'] ?? [];

            // Update project details
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, project_type = ?, project_date = ?, link = ?, description = ? WHERE prid = ? AND user_id = ?");
            $stmt->execute([$title, $project_type, $project_date, $link, $description, $project_id, $user_id]);

            // Handle features - delete all existing features first
            $stmt = $pdo->prepare("DELETE FROM project_features WHERE project_id = ?");
            $stmt->execute([$project_id]);

            // Insert updated features (filter out empty values)
            $features_filtered = array_filter($features, function ($feature) {
                return !empty(trim($feature));
            });

            if (!empty($features_filtered)) {
                $stmt = $pdo->prepare("INSERT INTO project_features (project_id, feature_desc) VALUES (?, ?)");

                foreach ($features_filtered as $feature) {
                    $stmt->execute([$project_id, trim($feature)]);
                }
            }

            $_SESSION['message'] = ' ✅ Project updated successfully!';
            $_SESSION['message_type'] = 'success';
            break;

        case 'delete_project':
            $project_id = $_POST['project_id'];
            // Delete project (features will be deleted automatically due to CASCADE)
            $stmt = $pdo->prepare("DELETE FROM projects WHERE prid = ? AND user_id = ?");
            $stmt->execute([$project_id, $user_id]);
            $_SESSION['message'] = ' 🗑️ Project deleted successfully!';
            $_SESSION['message_type'] = 'success';
            break;

        // ============ STRENGTHS ============
        case 'add_strength':
            $title = trim($_POST['strength_title']);
            $description = trim($_POST['description']);

            if (!empty($title)) {
                $stmt = $pdo->prepare("INSERT INTO strengths (user_id, title, description) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $title, $description]);
                $_SESSION['message'] = '💪 Strength added successfully!';
                $_SESSION['message_type'] = 'success';
            }
            break;

        case 'delete_strength':
            $strength_id = $_POST['strength_id'];
            $stmt = $pdo->prepare("DELETE FROM strengths WHERE sid = ? AND user_id = ?");
            $stmt->execute([$strength_id, $user_id]);
            $_SESSION['message'] = '🗑️ Strength deleted successfully!';
            $_SESSION['message_type'] = 'success';
            break;

        // ============ ACHIEVEMENTS ============
        case 'add_achievement':
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);

            if (!empty($title)) {
                $stmt = $pdo->prepare("INSERT INTO achievements (user_id, title, description) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $title, $description]);
                $_SESSION['message'] = '🏆 Achievement added successfully!';
                $_SESSION['message_type'] = 'success';
            }
            break;

        case 'delete_achievement':
            $achievement_id = $_POST['achievement_id'];
            $stmt = $pdo->prepare("DELETE FROM achievements WHERE acid = ? AND user_id = ?");
            $stmt->execute([$achievement_id, $user_id]);
            $_SESSION['message'] = '🗑️ Achievement deleted successfully!';
            $_SESSION['message_type'] = 'success';
            break;

        default:
            $_SESSION['message'] = 'Invalid action!';
            $_SESSION['message_type'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['message'] = 'Database error: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

header('Location: dashboard.php');
exit();
