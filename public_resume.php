<?php
// public_resume.php

session_start();
require_once 'config.php';

// Get user ID from URL
$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    $user_id = '1';
    // die('User ID not provided');
}

try {
    $pdo = getDBConnection();

    // Fetch user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE uid = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('User not found');
    }

    // Fetch personal info
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
    unset($project); // Break reference

    // Fetch strengths
    $stmt = $pdo->prepare("SELECT * FROM strengths WHERE user_id = ? ORDER BY sid");
    $stmt->execute([$user_id]);
    $strengths = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch achievements
    $stmt = $pdo->prepare("SELECT * FROM achievements WHERE user_id = ? ORDER BY acid");
    $stmt->execute([$user_id]);
    $achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Prepare personal info (matching your original resume.php structure)
$personalInfo = [
    'profile_pic' => $personal_info['profile_pic_path'] ?? 'assets/default.jpg',
    'name' => $personal_info['name'] ?? 'No Name',
    'title' => $personal_info['title'] ?? '',
    'phone' => $personal_info['phone'] ?? '',
    'email' => $personal_info['email'] ?? '',
    'github' => $personal_info['github_link'] ?? '',
    'location' => $personal_info['address_loc'] ?? ''
];

$aboutMe = $personal_info['about_me'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($personalInfo['name']); ?> Public Resume</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.4;
            color: #333;
            background: #ffffffff;
        }

        .no-print {
            text-align: center;
            padding: 20px;
            background: white;
            margin-bottom: 0px;
        }

        .download-btn {
            background: #b655daff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 5px;
        }

        .download-btn:hover {
            background: #922dc9ff;
        }

        .resume-container {
            max-width: 950px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .header-pic img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #e776daff 0%, #ac4fc6ff 100%);
            padding: 30px;
            color: white;
        }

        .header a {
            color: white;
            text-decoration: none;
        }

        .header a:hover {
            text-decoration: underline;
        }

        .header h1 {
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 0px;
        }

        .header .title {
            font-size: 20px;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .contact-info {
            display: flex;
            font-size: 12px;
            font-weight: 400;
            color: #f0f0f0;
            flex-wrap: wrap;
            max-width: 400px;
            text-align: left;
            gap: 5px;
        }

        .contact-info span {
            margin-right: 10px;
        }

        .contact-info a {
            color: white;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .main-content {
            display: flex;
        }

        .left-column {
            background: #fff0ffff;
            width: 280px;
            padding: 30px 25px;
        }

        .right-column {
            flex: 1;
            padding: 30px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            font-size: 18px;
            font-weight: 650;
            color: #954ec5ff;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .aboutMe {
            font-size: 13px;
            line-height: 1.6;
            color: #555;
            text-align: justify;
        }

        .project-item {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .project-item:last-child {
            border-bottom: none;
        }

        .project-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .project-type {
            color: #ac45a6ff;
            font-size: 13px;
            font-weight: 500;
        }

        .project-desc {
            font-size: 14px;
            margin-top: 10px;
            margin-bottom: 10px;
            line-height: 1.5;
            text-align: justify;
        }

        .project-meta {
            font-size: 13px;
            color: #9553ceff;
            margin-top: 8px;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .project-meta a {
            color: #6b1692ff;
            text-decoration: none;
        }

        .project-meta a:hover {
            text-decoration: underline;
        }

        .features h3 {
            font-size: 13px;
            color: #333;
            margin-bottom: 8px;
            margin-top: 10px;
        }

        .features {
            list-style: none;
        }

        .features li {
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 5px;
            padding-left: 35px;
            position: relative;
        }

        .features li:before {
            content: '•';
            color: #b82cb6ff;
            position: absolute;
            left: 20px;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 9px;
        }

        .skill-item {
            background: white;
            padding: 6px 6px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 620;
            text-align: center;
            border: 1px solid #a840ceff;
        }

        .skill-item:hover {
            background: #ffdefbff;
            border-color: #b639b0ff;
            transform: scale(1.08);
            transition: 0.2s;
        }

        .strength-item,
        .achievement-item {
            margin-bottom: 15px;
        }

        .strength-item h4,
        .achievement-item h4 {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .strength-item p,
        .achievement-item p {
            font-size: 12px;
            line-height: 1.4;
            color: #666;
        }

        .education-item {
            margin-bottom: 20px;
        }

        .education-item h4 {
            font-size: 14px;
            color: #333;
            margin-bottom: 3px;
        }

        .education-item .school {
            font-weight: 600;
            color: #555;
            font-size: 12px;
        }

        .education-item .year {
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
        }

        .public-notice {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            font-size: 14px;
            color: #1976D2;
        }

        @media print {
            body {
                background: white;
            }

            .no-print {
                display: none;
            }

            .resume-container {
                box-shadow: none;
            }

            .main-content {
                display: block;
            }

            .left-column {
                width: 100%;
                background: white;
            }

            .right-column {
                padding: 20px 0;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }

            .left-column {
                width: 100%;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }
        }

        .empty-state {
            text-align: center;
            padding: 20px;
            color: #999;
            font-style: italic;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="no-print">
        <a href="javascript:window.print()" class="download-btn">📄 Download/Print</a>
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && $_SESSION['user_id'] == $user_id): ?>
            <a href="dashboard.php" class="download-btn" style="background: #ab32c6ff;">Go to Dashboard</a>
        <?php endif; ?>
        <div class="public-notice">
            🌐 This is a public resume page - No login required to view
        </div>
    </div>

    <div class="resume-container">
        <!-- Header -->
        <div class="header">
            <div class="header-text">
                <h1><?php echo htmlspecialchars($personalInfo['name']); ?></h1>
                <div class="title"><?php echo htmlspecialchars($personalInfo['title']); ?></div>
                <div class="contact-info">
                    <?php if ($personalInfo['phone']): ?>
                        <span>📞 <?php echo htmlspecialchars($personalInfo['phone']); ?></span>
                    <?php endif; ?>

                    <?php if ($personalInfo['email']): ?>
                        <span>✉️ <?php echo htmlspecialchars($personalInfo['email']); ?></span>
                    <?php endif; ?>

                    <?php if ($personalInfo['github']): ?>
                        <span>🔗 <a href="<?php echo htmlspecialchars($personalInfo['github']); ?>" target="_blank">
                                <?php echo preg_replace('#^https?://(www\.)?github\.com/#', '', $personalInfo['github']); ?>
                            </a></span>
                    <?php endif; ?>

                    <?php if ($personalInfo['location']): ?>
                        <span>📍 <?php echo htmlspecialchars($personalInfo['location']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="header-pic">
                <img src="<?php echo htmlspecialchars($personal_info['profile_pic_path'] ?? 'assets/default.jpg'); ?>"
                    alt="Profile Picture"
                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22130%22 height=%22130%22%3E%3Ccircle fill=%22%23ddd%22 cx=%2265%22 cy=%2265%22 r=%2265%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-size=%2230%22%3E👤%3C/text%3E%3C/svg%3E'">
            </div>
        </div>

        <div class="main-content">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Skills -->
                <div class="section">
                    <h2>Skills</h2>
                    <?php if (!empty($skills)): ?>
                        <div class="skills-grid">
                            <?php foreach ($skills as $skill): ?>
                                <div class="skill-item"><?php echo htmlspecialchars($skill['skill_name']); ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">No skills added yet</div>
                    <?php endif; ?>
                </div>

                <!-- Education -->
                <div class="section">
                    <h2>Education</h2>
                    <?php if (!empty($education)): ?>
                        <?php foreach ($education as $edu): ?>
                            <div class="education-item">
                                <h4><?php echo htmlspecialchars($edu['degree']); ?></h4>
                                <div class="school"><?php echo htmlspecialchars($edu['school']); ?></div>
                                <div class="year">
                                    <?php echo htmlspecialchars($edu['year']); ?>
                                    <?php if ($edu['location']): ?>
                                        • <?php echo htmlspecialchars($edu['location']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No education records added yet</div>
                    <?php endif; ?>
                </div>

                <!-- Strengths -->
                <?php if (!empty($strengths)): ?>
                    <div class="section">
                        <h2>Strengths</h2>
                        <?php foreach ($strengths as $strength): ?>
                            <div class="strength-item">
                                <h4><?php echo htmlspecialchars($strength['title']); ?></h4>
                                <p><?php echo htmlspecialchars($strength['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Key achievements -->
                <?php if (!empty($achievements)): ?>
                    <div class="section">
                        <h2>Key Achievements</h2>
                        <?php foreach ($achievements as $achievement): ?>
                            <div class="achievement-item">
                                <h4><?php echo htmlspecialchars($achievement['title']); ?></h4>
                                <p><?php echo htmlspecialchars($achievement['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- About Me -->
                <?php if ($aboutMe): ?>
                    <div class="section">
                        <h2>About Me</h2>
                        <div class="aboutMe"><?php echo nl2br(htmlspecialchars($aboutMe)); ?></div>
                    </div>
                <?php endif; ?>

                <!-- Projects -->
                <div class="section">
                    <h2>Projects</h2>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                            <div class="project-item">
                                <div class="project-title"><?php echo htmlspecialchars($project['title']); ?></div>
                                <div class="project-type">
                                    <?php echo htmlspecialchars($project['project_type']); ?>
                                    <?php if ($project['project_date']): ?>
                                        • <?php echo htmlspecialchars($project['project_date']); ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($project['description']): ?>
                                    <div class="project-desc"><?php echo nl2br(htmlspecialchars($project['description'])); ?></div>
                                <?php endif; ?>

                                <?php if (!empty($project['features'])): ?>
                                    <ul class="features">
                                        <h3>Key Features:</h3>
                                        <?php foreach ($project['features'] as $feature): ?>
                                            <li><?php echo htmlspecialchars($feature['feature_desc']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if ($project['link']): ?>
                                    <div class="project-meta">
                                        <a href="<?php echo htmlspecialchars($project['link']); ?>" target="_blank">
                                            View Project →
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No projects added yet</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>