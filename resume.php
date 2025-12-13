<?php
// resume.php

session_start();

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

// Resume Data
// Personal Information
$personalInfo = [
    'name' => 'ANDREA SOPHIA D. MERCADO',
    'title' => 'Computer Science Student | Aspiring Web Developer',
    'phone' => '+63 966 567 8947',
    'email' => 'mercadoandrea1605@gmail.com',
    'github' => 'https://github.com/andengsome',
    'location' => 'Batangas, Philippines'
];

// About Me
$aboutMe = "As a 3rd year Computer Science student at Batangas State University, I have developed a strong foundation in programming and software development. My technical proficiency in Java, C++, PHP, and web technologies, coupled with my problem-solving skills and team collaboration abilities, have been demonstrated through various academic projects. I am passionate about creating practical solutions that address real-world problems and am eager to apply my skills in professional development environments.";

// Projects
$projects = [
    [
        'title' => 'Eat-o-Meter: Calorie Tracking System',
        'type' => 'Solo Project',
        'date' => 'December 19, 2024',
        'link' => 'https://github.com/andengsome/MercadoAndreaSophiaCS2102_OOPfinalproject',
        'description' => 'A console application designed to help users track their daily calorie intake and exercise, manage their health goals, and maintain a balanced lifestyle.',
        'features' => [
            'User authentication system',
            'Calculate and display daily calories',
            'Log items to diary',
            'View log history and weekly report',
            'Track weight progress',
            'Delete account'
        ]
    ],
    [
        'title' => 'Camp Alegria by AJN',
        'type' => 'Group Project',
        'date' => 'December 16, 2023',
        'link' => 'https://github.com/andengsome/Camp-Alegria-by-AJN',
        'description' => 'Offers a user-friendly interface for effortless reservations and food orders, streamlining the booking process while guests navigate seamlessly through an array of services including room booking, dining, and amenities management.',
        'features' => [
            'User registration and login system',
            'Room booking and food ordering',
            'Detailed room information',
            'Payment processing',
            'Guest contact and inquiry system',
            'Customer service integration',
            'Session-based cart system'
        ]
    ],
    [
        'title' => 'Dayaw: A Cultural Showcase Platform',
        'type' => 'Group Project',
        'date' => 'December 13, 2023',
        'link' => 'https://github.com/andengsome/Dayaw',
        'description' => 'A web-based cultural showcase platform promoting Filipino cultural heritage through digital marketplace featuring products from Luzon, Visayas, and Mindanao.',
        'features' => [
            'Regional showcase functionality',
            'Cultural products catalog',
            'Responsive web design',
            'Interactive user elements',
            'Complete website implementation'
        ]
    ]
];

// Education
$education = [
    [
        'degree' => 'BS in Computer Science',
        'school' => 'Batangas State University - Alangilan',
        'year' => '2023 - Present',
        'location' => 'Batangas City, Philippines',
    ],
    [
        'degree' => 'Science, Technology, Engineering, & Mathematics (STEM)',
        'school' => 'Lobo Senior High School',
        'year' => '2021 - 2023',
        'location' => 'Lobo, Batangas, Philippines'
    ],
    [
        'degree' => 'Junior High School',
        'school' => 'Masaguitsit-Banalo National High School',
        'year' => '2017 - 2021',
        'location' => 'Lobo, Batangas, Philippines'
    ],
    [
        'degree' => 'Elementary',
        'school' => 'Mabilog na Bundok Elementary School',
        'year' => '2011 - 2017',
        'location' => 'Lobo, Batangas, Philippines'
    ]
];

// Skills
$skills = [
    'Java', 'C++', 'C#', 'PostgreSQL',
    'HTML', 'CSS', 'MySQL', 'Python',
    'PHP', 'OOP', 'MariaDB',
];

// Strengths
$strengths = [
    [
        'title' => 'Problem Solving',
        'description' => 'Strong analytical skills demonstrated through complex project implementations, consistently finding innovative solutions to technical challenges.'
    ],
    [
        'title' => 'Technical Proficiency',
        'description' => 'Highly skilled in PHP, Laravel, JavaScript, and MySQL with hands-on experience in full-stack development projects.'
    ],
    [
        'title' => 'Team Collaboration',
        'description' => 'Excellent team player with experience leading development teams and collaborating effectively in group projects.'
    ]
];

// Key achievements
$achievements = [
    [
        'title' => 'Academic Excellence',
        'description' => 'Maintained 85% and above GPA while completing multiple complex programming projects and contributing to open-source initiatives.'
    ],
    [
        'title' => 'Project Leadership',
        'description' => 'Excellent team player with experience leading development teams and collaborating effectively in group projects.'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mercado - Resume</title>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
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
            padding: 12px 12px;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .download-btn:hover {
            background: #922dc9ff;
        }
        
        .resume-container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: white; 
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
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
        
        .header text { 
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
        
        .contact-info a{
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
            margin-bottom: 5px; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
        }
        
        .aboutMe { 
            font-size: 13px; 
            line-height: 1.2; 
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
        
        .project-header { 
            margin-bottom: 30px
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
            margin-bottom: 5px;
            margin-right: 20px; 
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
        }
        
        .features { 
            list-style: none; 
        }
        
        .features li { 
            font-size: 13px; 
            line-height: 1.5; 
            margin-bottom: 0px; 
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
        
        .strength-item, .achievement-item { 
            margin-bottom: 15px; 
        }
        
        .strength-item h4, .achievement-item h4 { 
            font-size: 14px; 
            color: #333; 
            margin-bottom: 5px; 
        }
        
        .strength-item p, .achievement-item p { 
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
        
        .education-item .date { 
            font-size: 10px; 
            color: #666; 
        }

        .education-item .year { 
            font-size: 10px; 
            color: #666; 
            margin-bottom: 10px;
        }
        
        @media print {
            body { background: white; }
            .no-print { display: none; }
            .resume-container { box-shadow: none; }
            .main-content { display: block; }
            .left-column { width: 100%; background: white; }
            .right-column { padding: 20px 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <a href="javascript:window.print()" class="download-btn">📄 Download/Print as PDF</a>
        <p style="margin-top: 10px; font-size: 14px; color: #666;">
    </div>

    <div style="position: fixed; top: 10px; right: 10px; z-index: 1000;">
        <a href="?logout=1"
            style="background: #bf1137ff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 14px;"
            onclick="return confirm('Are you sure you want to logout?')">
            Logout
        </a>
    </div>
   

    <div class="resume-container">
        <!-- Header -->
        <div class="header">
            <div class="header-text">
                <h1><?php echo $personalInfo['name']; ?></h1>
                <div class="title"><?php echo $personalInfo['title']; ?></div>
                <div class="contact-info">
                    <span>📞 <?php echo $personalInfo['phone']; ?></span>
                    <span>✉️ <?php echo $personalInfo['email']; ?></span>
                    <span>🔗 <a href="<?php echo $personalInfo['github']; ?>" target="_blank">
                        github.com/andengsome
                    </a></span>
                    <span>📍 <?php echo $personalInfo['location']; ?></span>
                </div>
            </div>
            
            <div class="header-pic">
                <img src="assets/mercado.jpg" alt="Profile Picture" class="profile-pic">
            </div>
        </div>

        <div class="main-content">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Skills -->
                <div class="section">
                    <h2>Skills</h2>
                    <div class="skills-grid">
                        <?php foreach ($skills as $skill): ?>
                            <div class="skill-item"><?php echo $skill; ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Education -->
                <div class="section">
                    <h2>Education</h2>
                    <?php foreach ($education as $edu): ?>
                        <div class="education-item">
                            <h4><?php echo $edu['degree']; ?></h4>
                            <div class="school"><?php echo $edu['school']; ?></div>
                            <div class="year"><?php echo $edu['year']; ?> • <?php echo $edu['location']; ?></div>
                            <?php if (isset($edu['status'])): ?>
                                <div class="year"><?php echo $edu['status']; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Strengths -->
                <div class="section">
                    <h2>Strengths</h2>
                    <?php foreach ($strengths as $strength): ?>
                        <div class="strength-item">
                            <h4><?php echo $strength['title']; ?></h4>
                            <p><?php echo $strength['description']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Key achievements -->
                <div class="section">
                    <h2>Key achievements</h2>
                    <?php foreach ($achievements as $achievement): ?>
                        <div class="achievement-item">
                            <h4><?php echo $achievement['title']; ?></h4>
                            <p><?php echo $achievement['description']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Column -->
            <div class="right-column">
                <!-- aboutMe -->
                <div class="section">
                    <h2>About Me</h2>
                    <div class="aboutMe"><?php echo $aboutMe; ?></div>
                </div>

                <!-- Projects -->
                <div class="section">
                    <h2>Projects</h2>
                    <?php foreach ($projects as $project): ?>
                        <div class="project-item">
                            <div class="project-header">
                                <div class="project-title"><?php echo $project['title']; ?></div>
                                <div class="project-type"><?php echo $project['type']; ?> • <?php echo $project['date']; ?>
                            </div>
                            <div class="project-desc"><?php echo $project['description']; ?></div>
                            <ul class="features">
                                <h3>Key Features:</h3>
                                <?php foreach ($project['features'] as $feature): ?>
                                    <li><?php echo $feature; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="project-meta">
                                <a href="<?php echo $project['link']; ?>" target="_blank">
                                    View Github
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>