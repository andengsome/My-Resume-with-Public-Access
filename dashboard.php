<?php
// dashboard.php - Complete with CRUD Interface

session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$pdo = getDBConnection();
$user_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Fetch personal_info data from database
$stmt = $pdo->prepare("SELECT * FROM personal_info WHERE user_id = ? ORDER BY piid");
$stmt->execute([$user_id]);
$personal_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch skills
$stmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = ? ORDER BY skid");
$stmt->execute([$user_id]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch education
$stmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? ORDER BY eid");
$stmt->execute([$user_id]);
$education = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch projects
$stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY prid");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch features for each project
foreach ($projects as &$project) {
    $stmt = $pdo->prepare("SELECT * FROM project_features WHERE project_id = ? ORDER BY pfid");
    $stmt->execute([$project['prid']]);
    $project['features'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($project);

// Fetch strengths
$stmt = $pdo->prepare("SELECT * FROM strengths WHERE user_id = ? ORDER BY sid");
$stmt->execute([$user_id]);
$strengths = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch achievements
$stmt = $pdo->prepare("SELECT * FROM achievements WHERE user_id = ? ORDER BY acid");
$stmt->execute([$user_id]);
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display session messages
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Edit Resume</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #e776daff 0%, #ac4fc6ff 100%);
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #ac4fc6ff 100%);
            padding: 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 28px;
        }

        .nav-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-buttons a {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .nav-buttons a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .nav-buttons b {
            padding: 10px 20px;
            background: rgba(223, 0, 0, 0.61);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .nav-buttons b:hover {
            background: rgba(255, 36, 36, 0.3);
            transform: translateY(-2px);
        }

        .content {
            padding: 40px;
        }

        .message {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-weight: 600;
            animation: slideIn 0.3s ease-out;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .public-link-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 4px solid #954ec5ff;
        }

        .public-link-box strong {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-size: 16px;
        }

        .public-link-box code {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            display: block;
            margin-bottom: 10px;
            word-break: break-all;
            font-family: monospace;
            font-size: 13px;
        }

        .public-link-box a {
            color: #954ec5ff;
            text-decoration: none;
            font-weight: 600;
        }

        .section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .section h2 {
            color: #954ec5ff;
            margin-bottom: 20px;
            font-size: 22px;
            border-bottom: 2px solid #954ec5ff;
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #954ec5ff;
            box-shadow: 0 0 0 3px rgba(149, 78, 197, 0.1);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn {
            background: linear-gradient(135deg, #e776daff 0%, #ac4fc6ff 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(149, 78, 197, 0.3);
        }

        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-add {
            background: #28a745;
        }

        .btn-edit {
            background: #e2cae3ff;
            color: #000;
        }

        .btn-delete {
            background: red;
        }

        .item-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }

        .item-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .item-content {
            flex: 1;
        }

        .item-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
            align-items: flex-start;
        }

        /* Update button size */
        .btn-small {
            padding: 6px 12px;
            font-size: 13px;
            white-space: nowrap;
        }

        .skill-tag {
            display: inline-block;
            background: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin: 5px;
            border: 1px solid #e0e0e0;
            position: relative;
        }

        .skill-tag button {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            margin-left: 8px;
            font-size: 18px;
            font-weight: bold;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #954ec5ff;
            font-size: 22px;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
        }

        .feature-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .feature-item input {
            flex: 1;
        }

        .feature-item button {
            padding: 10px;
            background: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-feature-btn {
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }

            .content {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📝 Edit Your Resume</h1>
            <div class="nav-buttons">
                <a href="public_resume.php?id=<?php echo $user_id; ?>" target="_blank">👁️ View Public Resume</a>
                <b href="?logout=1" onclick="return confirm('Are you sure you want to logout?')">🚪 Logout</b>
            </div>
        </div>

        <div class="content">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Public Link Box -->
            <div class="public-link-box">
                <strong>🔗 Your Public Resume URL:</strong>
                <code><?php echo "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/public_resume.php?id=" . $user_id; ?></code>
                <a href="public_resume.php?id=<?php echo $user_id; ?>" target="_blank">→ Open in New Tab</a>
            </div>

            <<!-- Profile Picture Section -->
                <div class="section">
                    <h2>Profile Picture</h2>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <?php if (!empty($personal_info['profile_pic_path']) && file_exists($personal_info['profile_pic_path'])): ?>
                            <img src="<?php echo htmlspecialchars($personal_info['profile_pic_path']); ?>"
                                alt="Profile Picture"
                                style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #954ec5ff;">
                        <?php else: ?>
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='150' height='150'%3E%3Ccircle fill='%23ddd' cx='75' cy='75' r='75'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23999' font-size='40'%3E👤%3C/text%3E%3C/svg%3E"
                                alt="No Profile Picture"
                                style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #ddd;">
                            <p style="color: #666; font-style: italic; margin-top: 10px;">No profile picture yet</p>
                        <?php endif; ?>
                    </div>
                    <form method="POST" action="upload_picture.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="profile_pic">Upload New Profile Picture:</label>
                            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" required
                                style="border: 2px dashed #954ec5ff; padding: 20px; background: #f8f9fa;">
                            <small style="display: block; margin-top: 5px; color: #666;">
                                📸 Max 5MB • Supported: JPG, PNG, GIF, WebP
                            </small>
                        </div>
                        <button type="submit" class="btn">📤 Upload Picture</button>
                    </form>
                </div>

                <!-- Basic Information Section - FIXED -->
                <div class="section">
                    <h2>Personal Information</h2>
                    <form method="POST" action="update_profile.php">
                        <input type="hidden" name="action" value="update_basic">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($personal_info['name'] ?? ''); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="title">Professional Title</label>
                                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($personal_info['title'] ?? ''); ?>" placeholder="e.g., Web Developer">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($personal_info['phone'] ?? ''); ?>" placeholder="+63 912 345 6789">
                            </div>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($personal_info['email'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="github">GitHub URL</label>
                                <input type="text" id="github" name="github" value="<?php echo htmlspecialchars($personal_info['github_link'] ?? ''); ?>" placeholder="https://github.com/username">
                            </div>

                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($personal_info['address_loc'] ?? ''); ?>" placeholder="City, Country">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="about_me">About Me / Professional Summary</label>
                            <textarea id="about_me" name="about_me" rows="5"><?php echo htmlspecialchars($personal_info['about_me'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn">💾 Update Personal Information</button>
                    </form>
                </div>

                <!-- Skills Section with CRUD -->
                <div class="section">
                    <h2>
                        <span>Skills (<?php echo count($skills); ?>)</span>
                        <button class="btn btn-small btn-add" onclick="openModal('skillModal')">+ Add Skill</button>
                    </h2>
                    <?php if (!empty($skills)): ?>
                        <div>
                            <?php foreach ($skills as $skill): ?>
                                <span class="skill-tag">
                                    <?php echo htmlspecialchars($skill['skill_name']); ?>
                                    <button onclick="deleteSkill(<?php echo $skill['skid']; ?>)" title="Delete">×</button>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">No skills added yet.
                            Click "+ Add Skill" to get started!</div>
                    <?php endif; ?>
                </div>

                <!-- Education Section with CRUD -->
                <div class="section">
                    <h2>
                        <span>Education (<?php echo count($education); ?>)</span>
                        <button class="btn btn-small btn-add" onclick="openModal('educationModal')">+ Add Education</button>
                    </h2>
                    <?php if (!empty($education)): ?>
                        <?php foreach ($education as $edu): ?>
                            <div class="item-card">
                                <div class="item-content">
                                    <strong><?php echo htmlspecialchars($edu['degree']); ?></strong><br>
                                    <span style="color: #666;"><?php echo htmlspecialchars($edu['school']); ?></span><br>
                                    <small style="color: #999;"><?php echo htmlspecialchars($edu['year']); ?> • <?php echo htmlspecialchars($edu['location']); ?></small>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-small btn-edit" onclick='openEditEducationModal(<?php echo json_encode($edu); ?>)'>Edit</button>
                                    <button class="btn btn-small btn-delete" onclick="deleteEducation(<?php echo $edu['eid']; ?>)"> x </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No education records added yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Projects Section with CRUD -->
                <div class="section">
                    <h2>
                        <span>Projects (<?php echo count($projects); ?>)</span>
                        <button class="btn btn-small btn-add" onclick="openModal('projectModal')">+ Add Project</button>
                    </h2>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                            <div class="item-card">
                                <div class="item-content">
                                    <strong><?php echo htmlspecialchars($project['title']); ?></strong><br>
                                    <small style="color: #999;"><?php echo htmlspecialchars($project['project_type']); ?> • <?php echo htmlspecialchars($project['project_date']); ?></small>
                                    <?php if ($project['description']): ?>
                                        <p style="margin-top: 10px; color: #666; font-size: 14px;"><?php echo htmlspecialchars(substr($project['description'], 0, 150)) . (strlen($project['description']) > 150 ? '...' : ''); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-small btn-edit" onclick='openEditProjectModal(<?php echo json_encode($project); ?>)'>Edit</button>
                                    <button class="btn btn-small btn-delete" onclick="deleteProject(<?php echo $project['prid']; ?>)">x </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No projects added yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Strengths Section with CRUD -->
                <div class="section">
                    <h2>
                        <span>Strengths (<?php echo count($strengths); ?>)</span>
                        <button class="btn btn-small btn-add" onclick="openModal('strengthModal')">+ Add Strength</button>
                    </h2>
                    <?php if (!empty($strengths)): ?>
                        <?php foreach ($strengths as $strength): ?>
                            <div class="item-card">
                                <div class="item-content">
                                    <strong><?php echo htmlspecialchars($strength['title']); ?></strong>
                                    <?php if ($strength['description']): ?>
                                        <p style="margin-top: 8px; color: #666; font-size: 14px;"><?php echo htmlspecialchars($strength['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-small btn-delete" onclick="deleteStrength(<?php echo $strength['sid']; ?>)"> x </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No strengths added yet.</div>
                    <?php endif; ?>
                </div>

                <!-- Achievements Section with CRUD -->
                <div class="section">
                    <h2>
                        <span>Key Achievements (<?php echo count($achievements); ?>)</span>
                        <button class="btn btn-small btn-add" onclick="openModal('achievementModal')">+ Add Achievement</button>
                    </h2>
                    <?php if (!empty($achievements)): ?>
                        <?php foreach ($achievements as $achievement): ?>
                            <div class="item-card">
                                <div class="item-content">
                                    <strong><?php echo htmlspecialchars($achievement['title']); ?></strong>
                                    <?php if ($achievement['description']): ?>
                                        <p style="margin-top: 8px; color: #666; font-size: 14px;"><?php echo htmlspecialchars($achievement['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="item-actions">
                                    <button class="btn btn-small btn-delete" onclick="deleteAchievement(<?php echo $achievement['acid']; ?>)">x </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No achievements added yet.</div>
                    <?php endif; ?>
                </div>
        </div>
    </div>

    <!-- Add Skill Modal -->
    <div id="skillModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Skill</h3>
                <button class="close-modal" onclick="closeModal('skillModal')">&times;</button>
            </div>
            <form method="POST" action="update_handler.php">
                <input type="hidden" name="action" value="add_skill">
                <div class="form-group">
                    <label>Skill Name *</label>
                    <input type="text" name="skill_name" required placeholder="e.g., PHP, JavaScript, Python">
                </div>
                <button type="submit" class="btn">Add Skill</button>
            </form>
        </div>
    </div>

    <!-- Add Education Modal -->
    <div id="educationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Education</h3>
                <button class="close-modal" onclick="closeModal('educationModal')">&times;</button>
            </div>
            <form method="POST" action="update_handler.php">
                <input type="hidden" name="action" value="add_education">
                <div class="form-group">
                    <label>Degree/Course *</label>
                    <input type="text" name="degree" required placeholder="e.g., BS in Computer Science">
                </div>
                <div class="form-group">
                    <label>School/University *</label>
                    <input type="text" name="school" required placeholder="e.g., Batangas State University">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Year</label>
                        <input type="text" name="year" placeholder="e.g., 2023 - Present">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="address_loc" placeholder="e.g., Batangas City">
                    </div>
                </div>
                <button type="submit" class="btn">Add Education</button>
            </form>
        </div>
    </div>

    <!-- Edit Education Modal -->
    <div id="editEducationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Education</h3>
                <button class="close-modal" onclick="closeModal('editEducationModal')">&times;</button>
            </div>
            <form method="POST" action="update_handler.php" id="editEducationForm">
                <input type="hidden" name="action" value="edit_education">
                <input type="hidden" name="edu_id" id="edit_edu_id">
                <div class="form-group">
                    <label>Degree/Course *</label>
                    <input type="text" name="degree" id="edit_degree" required>
                </div>
                <div class="form-group">
                    <label>School/University *</label>
                    <input type="text" name="school" id="edit_school" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Year</label>
                        <input type="text" name="year" id="edit_year">
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="address_loc" id="edit_address_loc">
                    </div>
                </div>
                <button type="submit" class="btn">Update Education</button>
            </form>
        </div>
    </div>

    <!-- Add Project Modal -->
    <div id="projectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Project</h3>
                <button class="close-modal" onclick="closeModal('projectModal')">&times;</button>
            </div>
            <form method="POST" action="update_handler.php">
                <input type="hidden" name="action" value="add_project">
                <div class="form-group">
                    <label>Project Title *</label>
                    <input type="text" name="title" required placeholder="e.g., E-commerce Website">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Project Type</label>
                        <input type="text" name="project_type" placeholder="e.g., Solo Project">
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="text" name="project_date" placeholder="e.g., December 2024">
                    </div>
                </div>
                <div class="form-group">
                    <label>Project Link (GitHub, etc.)</label>
                    <input type="text" name="link" placeholder="https://github_link.com/username/project">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe your project..."></textarea>
                </div>
                <div class="form-group">
                    <label>Key Features</label>
                    <div id="features-container">
                        <div class="feature-item">
                            <input type="text" name="features[]" placeholder="Feature 1">
                        </div>
                    </div>
                    <button type="button" class="add-feature-btn" onclick="addFeatureInput()">+ Add Feature</button>
                </div>
                <button type="submit" class="btn">Add Project</button>
            </form>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Project</h3>
                <button class="close-modal" onclick="closeModal('editProjectModal')">&times;</button>
            </div>
            <form method="POST" action="update_handler.php">
                <input type="hidden" name="action" value="edit_project">
                <input type="hidden" name="project_id" id="edit_prid">
                <div class="form-group">
                    <label>Project Title *</label>
                    <input type="text" name="title" id="edit_title" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Project Type</label>
                        <input type="text" name="project_type" id="edit_type">
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="text" name="project_date" id="edit_date">
                    </div>
                </div>
                <div class="form-group">
                    <label>Project Link (GitHub, etc.)</label>
                    <input type="text" name="link" id="edit_link">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description"></textarea>
                </div>

                <div class="form-group">
                    <label>Key Features</label>
                    <div id="edit-features-container">
                        <!-- Features will be dynamically loaded here -->
                    </div>
                    <button type="button" class="add-feature-btn" onclick="addEditFeatureInput()">+ Add Feature</button>
                </div>
                <button type="submit" class="btn">Update Project</button>
            </form>
        </div>
    </div>

    <!-- Add Strength Modal -->
    <div id="strengthModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Strength</h3>
                <button class="close-modal" onclick="closeModal('strengthModal')">&times;</button>
            </div>
            <form method="POST" action="update_handler.php">
                <input type="hidden" name="action" value="add_strength">
                <div class="form-group">
                    <label>Strength Title *</label>
                    <input type="text" name="strength_title" required placeholder="e.g., Problem Solving">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe this strength..."></textarea>
                </div>
                <button type="submit" class="btn">Add Strength</button>
            </form>
        </div>
    </div>

    <!-- Add Achievement Modal -->
    <div id="achievementModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Achievement</h3>
                <button class="close-modal" onclick="closeModal('achievementModal')">&times;</button>
            </div>
            <form method="POST" action="update_handler.php">
                <input type="hidden" name="action" value="add_achievement">
                <div class="form-group">
                    <label>Achievement Title *</label>
                    <input type="text" name="title" required placeholder="e.g., Academic Excellence">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe this achievement..."></textarea>
                </div>
                <button type="submit" class="btn">Add Achievement</button>
            </form>
        </div>
    </div>

    <script>
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.x('active');
            }
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Add Feature Input for ADD modal
        function addFeatureInput() {
            const container = document.getElementById('features-container');
            const featureCount = container.children.length + 1;
            const div = document.createElement('div');
            div.className = 'feature-item';
            div.innerHTML = `
            <input type="text" name="features[]" placeholder="Feature ${featureCount}">
            <button type="button" onclick="this.parentElement.remove()"> × </button>
        `;
            container.appendChild(div);
        }

        // Add Feature Input for EDIT modal
        function addEditFeatureInput() {
            const container = document.getElementById('edit-features-container');
            const featureCount = container.children.length + 1;
            const div = document.createElement('div');
            div.className = 'feature-item';
            div.innerHTML = `
            <input type="text" name="features[]" placeholder="Feature ${featureCount}">
            <button type="button" onclick="this.parentElement.remove()"> x </button>
        `;
            container.appendChild(div);
        }

        // Edit Education Modal
        function openEditEducationModal(edu) {
            document.getElementById('edit_edu_id').value = edu.eid;
            document.getElementById('edit_degree').value = edu.degree;
            document.getElementById('edit_school').value = edu.school;
            document.getElementById('edit_year').value = edu.year || '';
            document.getElementById('edit_address_loc').value = edu.address_loc || '';
            openModal('editEducationModal');
        }

        // Edit Project Modal - FIXED VERSION
        function openEditProjectModal(project) {
            console.log('Project data:', project); // Debug

            // Set basic project fields
            document.getElementById('edit_prid').value = project.prid;
            document.getElementById('edit_title').value = project.title || '';
            document.getElementById('edit_type').value = project.project_type || '';
            document.getElementById('edit_date').value = project.project_date || '';
            document.getElementById('edit_link').value = project.link || '';
            document.getElementById('edit_description').value = project.description || '';

            // Load existing features
            const container = document.getElementById('edit-features-container');
            container.innerHTML = ''; // Clear previous features

            console.log('Features:', project.features); // Debug

            // Check if features exist and is an array
            if (project.features && Array.isArray(project.features) && project.features.length > 0) {
                project.features.forEach((feature, index) => {
                    const div = document.createElement('div');
                    div.className = 'feature-item';
                    // Use feature_desc from your database
                    const featureName = feature.feature_desc || '';
                    div.innerHTML = `
                    <input type="text" name="features[]" value="${escapeHtml(featureName)}" placeholder="Feature ${index + 1}">
                    <button type="button" onclick="this.parentElement.x ()"> × </button>
                `;
                    container.appendChild(div);
                });
            } else {
                // Add one empty feature input if no features exist
                addEditFeatureInput();
            }

            openModal('editProjectModal');
        }

        // Delete Functions
        function deleteSkill(id) {
            if (confirm('Are you sure you want to delete this skill?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'update_handler.php';
                form.innerHTML = `
                <input type="hidden" name="action" value="delete_skill">
                <input type="hidden" name="skill_id" value="${id}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteEducation(id) {
            if (confirm('Are you sure you want to delete this education entry?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'update_handler.php';
                form.innerHTML = `
                <input type="hidden" name="action" value="delete_education">
                <input type="hidden" name="edu_id" value="${id}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteProject(id) {
            if (confirm('Are you sure you want to delete this project?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'update_handler.php';
                form.innerHTML = `
                <input type="hidden" name="action" value="delete_project">
                <input type="hidden" name="project_id" value="${id}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteStrength(id) {
            if (confirm('Are you sure you want to delete this strength?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'update_handler.php';
                form.innerHTML = `
                <input type="hidden" name="action" value="delete_strength">
                <input type="hidden" name="strength_id" value="${id}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteAchievement(id) {
            if (confirm('Are you sure you want to delete this achievement?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'update_handler.php';
                form.innerHTML = `
                <input type="hidden" name="action" value="delete_achievement">
                <input type="hidden" name="achievement_id" value="${id}">
            `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>