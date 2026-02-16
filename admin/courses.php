<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $name = sanitize($_POST['name']);
        $department_id = $_POST['department_id'];
        $stmt = $pdo->prepare("INSERT INTO courses (name, department_id) VALUES (?, ?)");
        $stmt->execute([$name, $department_id]);
        $message = "Course added successfully.";
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = sanitize($_POST['name']);
        $department_id = $_POST['department_id'];
        $stmt = $pdo->prepare("UPDATE courses SET name = ?, department_id = ? WHERE id = ?");
        $stmt->execute([$name, $department_id, $id]);
        $message = "Course updated successfully.";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Course deleted successfully.";
    }
}

$courses = getCourses();
$departments = getDepartments();
$edit_course = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_course = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Manage Courses</h2>
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
                            <li><a href="dashboard.php">Dashboard</a></li>
                            <li><a href="departments.php">Departments</a></li>
                            <li><a href="courses.php" class="active">Courses</a></li>
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
            <?php if ($message): ?>
                <div class="success"><?php echo $message; ?></div>
            <?php endif; ?>

            <h3><?php echo $edit_course ? 'Edit' : 'Add'; ?> Course</h3>
            <form method="post">
                <?php if ($edit_course): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_course['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="name">Course Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $edit_course ? $edit_course['name'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="department_id">Department:</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo ($edit_course && $edit_course['department_id'] == $dept['id']) ? 'selected' : ''; ?>><?php echo $dept['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="<?php echo $edit_course ? 'edit' : 'add'; ?>"><?php echo $edit_course ? 'Update' : 'Add'; ?> Course</button>
                <?php if ($edit_course): ?>
                    <a href="courses.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>

            <h3>Courses List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?php echo $course['id']; ?></td>
                            <td><?php echo $course['name']; ?></td>
                            <td><?php echo $course['department_name']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $course['id']; ?>" class="btn">Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
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