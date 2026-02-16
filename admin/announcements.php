<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_announcement'])) {
        $title = sanitize($_POST['title']);
        $content = sanitize($_POST['content']);
        $type = sanitize($_POST['type']);

        if (empty($title) || empty($content) || empty($type)) {
            $error = 'All fields are required.';
        } else {
            $file_path = null;

            // Handle file upload - required for exam_date announcements
            if ($type === 'exam_date') {
                if (!isset($_FILES['timetable_file']) || $_FILES['timetable_file']['error'] !== UPLOAD_ERR_OK) {
                    $error = 'Time table PDF is required for exam date announcements.';
                } else {
                    $upload_dir = '../uploads/';
                    $file_name = basename($_FILES['timetable_file']['name']);
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    // Allow only PDF files for exam date announcements
                    if ($file_ext !== 'pdf') {
                        $error = 'Only PDF files are allowed for exam time tables.';
                    } else {
                        // Generate unique filename
                        $unique_name = 'comprehensive_timetable_' . uniqid() . '.pdf';
                        $target_path = $upload_dir . $unique_name;

                        if (move_uploaded_file($_FILES['timetable_file']['tmp_name'], $target_path)) {
                            $file_path = $unique_name;
                        } else {
                            $error = 'Failed to upload PDF file.';
                        }
                    }
                }
            } elseif (isset($_FILES['timetable_file']) && $_FILES['timetable_file']['error'] === UPLOAD_ERR_OK) {
                // Optional file upload for other announcement types
                $upload_dir = '../uploads/';
                $file_name = basename($_FILES['timetable_file']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Allow only PDF files for other types too
                $allowed_exts = ['pdf'];
                if (!in_array($file_ext, $allowed_exts)) {
                    $error = 'Only PDF files are allowed.';
                } else {
                    // Generate unique filename
                    $unique_name = uniqid() . '_' . $file_name;
                    $target_path = $upload_dir . $unique_name;

                    if (move_uploaded_file($_FILES['timetable_file']['tmp_name'], $target_path)) {
                        $file_path = $unique_name;
                    } else {
                        $error = 'Failed to upload file.';
                    }
                }
            }

            if (!$error) {
                $stmt = $pdo->prepare("INSERT INTO announcements (title, content, type, file_path, created_by) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$title, $content, $type, $file_path, $_SESSION['admin_id']])) {
                    $message = 'Announcement added successfully.';
                } else {
                    $error = 'Failed to add announcement.';
                }
            }
        }
    } elseif (isset($_POST['delete_announcement'])) {
        $id = (int)$_POST['announcement_id'];

        // Get file path before deleting
        $stmt = $pdo->prepare("SELECT file_path FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        $announcement = $stmt->fetch();

        // Delete the announcement
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        if ($stmt->execute([$id])) {
            // Delete the file if it exists
            if ($announcement && $announcement['file_path']) {
                $file_path = '../uploads/' . $announcement['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            $message = 'Announcement deleted successfully.';
        } else {
            $error = 'Failed to delete announcement.';
        }
    }
}

$announcements = getAnnouncements();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Manage Announcements</h2>
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
                <div class="nav-container">
                    <div class="nav-section">
                        <h4>Management</h4>
                        <ul>
                            <li><a href="dashboard.php">Dashboard</a></li>
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
                            <li><a href="announcements.php" class="active">Announcements</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="content">
                <h3>Add New Announcement</h3>
                <?php if ($message): ?>
                    <div class="success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="form">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <select id="type" name="type" required>
                            <option value="">Select Type</option>
                            <option value="exam_date">Exam Date</option>
                            <option value="result">Result</option>
                            <option value="important">Important</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="content">Content:</label>
                        <textarea id="content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="timetable_file">Comprehensive Time Table PDF (Required for Exam Date announcements):</label>
                        <input type="file" id="timetable_file" name="timetable_file" accept=".pdf" required>
                        <small style="color: #666; display: block; margin-top: 5px;">Upload a single PDF containing ALL examination time tables for all departments and subjects</small>
                    </div>
                    <button type="submit" name="add_announcement" class="btn btn-success">Add Announcement</button>
                </form>

                <h3>Existing Announcements</h3>
                <?php if (empty($announcements)): ?>
                    <div class="no-data">
                        <p>No announcements found.</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>File</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($announcements as $announcement): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $announcement['type'])); ?></td>
                                    <td>
                                        <?php if ($announcement['file_path']): ?>
                                            <a href="../uploads/<?php echo htmlspecialchars($announcement['file_path']); ?>" target="_blank" class="btn btn-sm" style="background-color: #17a2b8; color: white;">Download</a>
                                        <?php else: ?>
                                            <span style="color: #999;">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($announcement['created_by_name'] ?? 'System'); ?></td>
                                    <td><?php echo date('d-m-Y H:i', strtotime($announcement['created_at'])); ?></td>
                                    <td>
                                        <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                            <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                            <button type="submit" name="delete_announcement" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
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