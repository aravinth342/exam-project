<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = sanitize($_POST['name']);
        $course_id = $_POST['course_id'];
        $semester = $_POST['semester'];
        $stmt = $pdo->prepare("INSERT INTO subjects (name, course_id, semester) VALUES (?, ?, ?)");
        $stmt->execute([$name, $course_id, $semester]);
        $message = "Subject added successfully.";
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = sanitize($_POST['name']);
        $course_id = $_POST['course_id'];
        $semester = $_POST['semester'];
        $stmt = $pdo->prepare("UPDATE subjects SET name = ?, course_id = ?, semester = ? WHERE id = ?");
        $stmt->execute([$name, $course_id, $semester, $id]);
        $message = "Subject updated successfully.";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Subject deleted successfully.";
    }
}

$subjects = getSubjects();
$courses = getCourses();
$edit_subject = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_subject = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Manage Subjects</h2>
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
                            <li><a href="subjects.php" class="active">Subjects</a></li>
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
            <?php if ($message): ?>
                <div class="success"><?php echo $message; ?></div>
            <?php endif; ?>

            <h3><?php echo $edit_subject ? 'Edit' : 'Add'; ?> Subject</h3>
            <form method="post">
                <?php if ($edit_subject): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_subject['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="name">Subject Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $edit_subject ? $edit_subject['name'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="course_id">Course:</label>
                    <select id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo ($edit_subject && $edit_subject['course_id'] == $course['id']) ? 'selected' : ''; ?>><?php echo $course['name']; ?> (<?php echo $course['department_name']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="semester">Semester:</label>
                    <input type="number" id="semester" name="semester" min="1" max="6" value="<?php echo $edit_subject ? $edit_subject['semester'] : ''; ?>" required>
                </div>
                <button type="submit" name="<?php echo $edit_subject ? 'edit' : 'add'; ?>"><?php echo $edit_subject ? 'Update' : 'Add'; ?> Subject</button>
                <?php if ($edit_subject): ?>
                    <a href="subjects.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>

            <h3>Subjects List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?php echo $subject['id']; ?></td>
                            <td><?php echo $subject['name']; ?></td>
                            <td><?php echo $subject['course_name']; ?></td>
                            <td><?php echo $subject['semester']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $subject['id']; ?>" class="btn">Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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