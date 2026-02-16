<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Chendhuran Polytechnic College</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="diulogoside.png" type="image/png">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <img src="diulogoside.png" alt="College Logo" class="logo">
                <div class="header-text">
                    <h1>Chendhuran Polytechnic College</h1>
                    <p>Exam Hall Arrangement System</p>
                </div>
            </div>
        </header>

        <nav>
            <div class="nav-header">
                <button class="hamburger-btn" id="hamburger-btn" aria-label="Toggle navigation menu">
                    <svg class="hamburger-icon" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
            <div class="nav-dropdown" id="nav-dropdown">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="announcements.php" class="active">Announcements</a></li>
                    <li><a href="admin/login.php">Admin Login</a></li>
                </ul>
            </div>
        </nav>

        <main>
            <div class="content">
                <h2>Latest Announcements</h2>

                <?php
                require_once 'includes/functions.php';
                $announcements = getAnnouncements();

                if (empty($announcements)) {
                    echo '<div class="no-data">';
                    echo '<p>No announcements available at the moment.</p>';
                    echo '</div>';
                } else {
                    foreach ($announcements as $announcement) {
                        $typeClass = 'announcement-' . $announcement['type'];
                        $typeLabel = ucfirst(str_replace('_', ' ', $announcement['type']));
                        echo '<div class="announcement-card ' . $typeClass . '">';
                        echo '<div class="announcement-header">';
                        echo '<h3>' . htmlspecialchars($announcement['title']) . '</h3>';
                        echo '<span class="announcement-type">' . $typeLabel . '</span>';
                        echo '</div>';
                        echo '<div class="announcement-content">';
                        echo '<p>' . nl2br(htmlspecialchars($announcement['content'])) . '</p>';
                        if ($announcement['type'] === 'exam_date' && $announcement['file_path']) {
                            echo '<div class="download-section" style="margin-top: 20px; padding: 15px; background-color: #e8f4fd; border: 2px solid #3498db; border-radius: 8px;">';
                            echo '<h4 style="margin: 0 0 10px 0; color: #2c3e50; font-size: 1.1em;">📄 Comprehensive Examination Time Table</h4>';
                            echo '<p style="margin: 0 0 15px 0; color: #555; font-size: 0.9em;">Download the complete PDF containing all examination schedules for all departments and subjects.</p>';
                            echo '<a href="uploads/' . htmlspecialchars($announcement['file_path']) . '" target="_blank" class="btn" style="background-color: #3498db; color: white; text-decoration: none; padding: 12px 20px; border-radius: 6px; display: inline-block; font-weight: bold; font-size: 1em;">';
                            echo '📥 Download Complete Time Table (PDF)</a>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '<div class="announcement-footer">';
                        echo '<small>Posted on ' . date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) . '</small>';
                        if ($announcement['created_by_name']) {
                            echo '<small> by ' . htmlspecialchars($announcement['created_by_name']) . '</small>';
                        }
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>

            </div>
        </main>

        <footer>
            <p>&copy; 2024 Chendhuran Polytechnic College. All rights reserved.</p>
            <p>Developed by the College Administration</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const navDropdown = document.getElementById('nav-dropdown');
            const hamburgerIcon = hamburgerBtn.querySelector('.hamburger-icon');

            // Toggle menu function
            function toggleMenu() {
                const isOpen = navDropdown.classList.contains('open');
                
                if (isOpen) {
                    // Close menu
                    navDropdown.classList.remove('open');
                    hamburgerIcon.innerHTML = '<path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
                } else {
                    // Open menu
                    navDropdown.classList.add('open');
                    hamburgerIcon.innerHTML = '<path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
                }
            }

            // Hamburger button click
            hamburgerBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMenu();
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!hamburgerBtn.contains(e.target) && !navDropdown.contains(e.target)) {
                    navDropdown.classList.remove('open');
                    hamburgerIcon.innerHTML = '<path d="M3 12h18M3 6h18M3 18h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && navDropdown.classList.contains('open')) {
                    toggleMenu();
                }
            });
        });
    </script>
</body>
</html>