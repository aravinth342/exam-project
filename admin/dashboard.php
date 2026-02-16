<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$departments = $pdo->query("SELECT COUNT(*) as count FROM departments")->fetch()['count'];
$courses = $pdo->query("SELECT COUNT(*) as count FROM courses")->fetch()['count'];
$subjects = $pdo->query("SELECT COUNT(*) as count FROM subjects")->fetch()['count'];
$students = $pdo->query("SELECT COUNT(*) as count FROM students")->fetch()['count'];
$halls = $pdo->query("SELECT COUNT(*) as count FROM exam_halls")->fetch()['count'];
$exams = $pdo->query("SELECT COUNT(*) as count FROM exams")->fetch()['count'];
$results = $pdo->query("SELECT COUNT(*) as count FROM results")->fetch()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Exam Hall Arrangement System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <img src="../diulogoside.png" alt="Chendhuran Polytechnic College Logo" class="college-logo">
                <div class="college-info">
                    <h1>Chendhuran Polytechnic College</h1>
                    <p>Lenavilaku, Pudukkottai, Tamil Nadu</p>
                    <h2>Exam Hall Arrangement System - Admin Dashboard</h2>
                </div>
            </div>
        </header>

        <nav>
            <div class="nav-header">
                <button class="hamburger-menu" id="hamburger-menu" aria-label="Toggle navigation menu">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3">
                        <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                    </svg>
                </button>
                <span class="nav-title">Admin Menu</span>
            </div>
            <div class="nav-dropdown" id="nav-dropdown">
                <div class="nav-container">
                    <div class="nav-section">
                        <h4>Management</h4>
                        <ul>
                            <li><a href="dashboard.php" class="active">Dashboard</a></li>
                            <li><a href="departments.php">Departments</a></li>
                            <li><a href="courses.php">Courses</a></li>
                            <li><a href="subjects.php">Subjects</a></li>
                            <li><a href="students.php">Students</a></li>
                        </ul>
                    </div>
                    <div class="nav-section">
                        <h4>Examinations</h4>
                        <ul>
                            <li><a href="halls.php">Exam Halls</a></li>
                            <li><a href="exams.php">Exams</a></li>
                            <li><a href="allocation.php">Hall Allocation</a></li>
                            <li><a href="results.php">Results</a></li>
                        </ul>
                    </div>
                    <div class="nav-section">
                        <h4>Reports & Communication</h4>
                        <ul>
                            <li><a href="reports.php">Reports</a></li>
                            <li><a href="announcements.php">Announcements</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <h3>Welcome, <?php echo $_SESSION['admin_username']; ?>!</h3>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h4>Departments</h4>
                    <div class="number"><?php echo $departments; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Courses</h4>
                    <div class="number"><?php echo $courses; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Subjects</h4>
                    <div class="number"><?php echo $subjects; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Students</h4>
                    <div class="number"><?php echo $students; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Exam Halls</h4>
                    <div class="number"><?php echo $halls; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Exams</h4>
                    <div class="number"><?php echo $exams; ?></div>
                </div>
                <div class="stat-card">
                    <h4>Results</h4>
                    <div class="number"><?php echo $results; ?></div>
                </div>
            </div>

            <p>Use the navigation menu to manage the system components and generate reports.</p>
        </main>
    </div>

    <script>
        // Hamburger menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerMenu = document.getElementById('hamburger-menu');
            const navDropdown = document.getElementById('nav-dropdown');

            if (hamburgerMenu && navDropdown) {
                hamburgerMenu.addEventListener('click', function() {
                    navDropdown.classList.toggle('open');
                    
                    // Update hamburger icon (optional: could change to X when open)
                    const svg = hamburgerMenu.querySelector('svg');
                    if (navDropdown.classList.contains('open')) {
                        // Change to close icon when menu is open
                        svg.innerHTML = '<path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/>';
                    } else {
                        // Change back to hamburger icon when menu is closed
                        svg.innerHTML = '<path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>';
                    }
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!hamburgerMenu.contains(event.target) && !navDropdown.contains(event.target)) {
                        navDropdown.classList.remove('open');
                        // Reset hamburger icon
                        const svg = hamburgerMenu.querySelector('svg');
                        svg.innerHTML = '<path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>';
                    }
                });

                // Close menu when a link is clicked
                const navLinks = navDropdown.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navDropdown.classList.remove('open');
                        // Reset hamburger icon
                        const svg = hamburgerMenu.querySelector('svg');
                        svg.innerHTML = '<path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>';
                    });
                });
            }
        });
    </script>
</body>
</html>