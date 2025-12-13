-- PERSONAL INFO
UPDATE personal_info
SET 
    user_id = 1,
    profile_pic_path = 'assets/profile_pics/1/profile_1_000.JPG',
    name = 'ANDREA SOPHIA D. MERCADO',
    title = 'Computer Science Student | Aspiring Web Developer',
    phone = '+63 966 567 8947',
    email = 'mercadoandrea1605@gmail.com',
    github_link = 'https://github.com/andengsome',
    address_loc = 'Batangas, Philippines',
    about_me = 'As a 3rd year Computer Science student at Batangas State University, I have developed a strong foundation in programming and software development. My technical proficiency in Java, C++, PHP, and web technologies, coupled with my problem-solving skills and team collaboration abilities, have been demonstrated through various academic projects. I am passionate about creating practical solutions that address real-world problems and am eager to apply my skills in professional development environments.'
WHERE piid = 1;

-- Project 1: Eat-o-Meter
INSERT INTO projects (user_id, title, project_type, project_date, link, description)
VALUES (
    1,
    'Eat-o-Meter: Calorie Tracking System',
    'Solo Project',
    '2024-12-19',
    'https://github.com/andengsome/MercadoAndreaSophiaCS2102_OOPfinalproject',
    'A console application designed to help users track their daily calorie intake and exercise, manage their health goals, and maintain a balanced lifestyle.'
);

-- Project 1 Features
INSERT INTO project_features (project_id, feature_desc) VALUES
(1, 'User authentication system'),
(1, 'Calculate and display daily calories'),
(1, 'Log items to diary'),
(1, 'View log history and weekly report'),
(1, 'Track weight progress'),
(1, 'Delete account');

-- Project 2: Camp Alegria by AJN
INSERT INTO projects (user_id, title, project_type, project_date, link, description)
VALUES (
    1,
    'Camp Alegria by AJN',
    'Group Project',
    '2023-12-16',
    'https://github.com/andengsome/Camp-Alegria-by-AJN',
    'Offers a user-friendly interface for effortless reservations and food orders, streamlining the booking process while guests navigate seamlessly through an array of services including room booking, dining, and amenities management.'
);

-- Project 2 Features (assuming project_id is 2)
INSERT INTO project_features (project_id, feature_desc) VALUES
(2, 'User registration and login system'),
(2, 'Room booking and food ordering'),
(2, 'Detailed room information'),
(2, 'Payment processing'),
(2, 'Guest contact and inquiry system'),
(2, 'Customer service integration'),
(2, 'Session-based cart system');

-- Project 3: Dayaw
INSERT INTO projects (user_id, title, project_type, project_date, link, description)
VALUES (
    1,
    'Dayaw: A Cultural Showcase Platform',
    'Group Project',
    '2023-12-13',
    'https://github.com/andengsome/Dayaw',
    'A web-based cultural showcase platform promoting Filipino cultural heritage through digital marketplace featuring products from Luzon, Visayas, and Mindanao.'
);

-- Project 3 Features (assuming project_id is 3)
INSERT INTO project_features (project_id, feature_desc) VALUES
(3, 'Regional showcase functionality'),
(3, 'Cultural products catalog'),
(3, 'Responsive web design'),
(3, 'Interactive user elements'),
(3, 'Complete website implementation');

-- EDUCATION
INSERT INTO education (user_id, degree, school, year, location) VALUES
(1, 'BS in Computer Science', 'Batangas State University - Alangilan', '2023 - Present', 'Batangas City, Philippines'),
(1, 'Science, Technology, Engineering, & Mathematics (STEM)', 'Lobo Senior High School', '2021 - 2023', 'Lobo, Batangas, Philippines'),
(1, 'Junior High School', 'Masaguitsit-Banalo National High School', '2017 - 2021', 'Lobo, Batangas, Philippines'),
(1, 'Elementary', 'Mabilog na Bundok Elementary School', '2011 - 2017', 'Lobo, Batangas, Philippines');

-- SKILLS
INSERT INTO skills (user_id, skill_name, proficiency_level) VALUES
(1, 'Java', NULL),
(1, 'C++', NULL),
(1, 'C#', NULL),
(1, 'PostgreSQL', NULL),
(1, 'HTML', NULL),
(1, 'CSS', NULL),
(1, 'MySQL', NULL),
(1, 'Python', NULL),
(1, 'PHP', NULL),
(1, 'OOP', NULL),
(1, 'MariaDB', NULL);

-- STRENGTHS
INSERT INTO strengths (user_id, title, description) VALUES
(1, 'Problem Solving', 'Strong analytical skills demonstrated through complex project implementations, consistently finding innovative solutions to technical challenges.'),
(1, 'Technical Proficiency', 'Highly skilled in PHP, Laravel, JavaScript, and MySQL with hands-on experience in full-stack development projects.'),
(1, 'Team Collaboration', 'Excellent team player with experience leading development teams and collaborating effectively in group projects.');

-- ACHIEVEMENTS
INSERT INTO achievements (user_id, title, description) VALUES
(1, 'Academic Excellence', 'Maintained 85% and above GPA while completing multiple complex programming projects and contributing to open-source initiatives.'),
(1, 'Project Leadership', 'Excellent team player with experience leading development teams and collaborating effectively in group projects.');