<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $register_number = sanitize($_POST['register_number']);
        $name = sanitize($_POST['name']);
        $department_id = $_POST['department_id'];
        $year = $_POST['year'];
        $semester = $_POST['semester'];
        $stmt = $pdo->prepare("INSERT INTO students (register_number, name, department_id, year, semester) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$register_number, $name, $department_id, $year, $semester]);
        $message = "Student added successfully.";
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $register_number = sanitize($_POST['register_number']);
        $name = sanitize($_POST['name']);
        $department_id = $_POST['department_id'];
        $year = $_POST['year'];
        $semester = $_POST['semester'];
        $stmt = $pdo->prepare("UPDATE students SET register_number = ?, name = ?, department_id = ?, year = ?, semester = ? WHERE id = ?");
        $stmt->execute([$register_number, $name, $department_id, $year, $semester, $id]);
        $message = "Student updated successfully.";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Student deleted successfully.";
    }
}

$students = getStudents();
$departments = getDepartments();
$edit_student = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_student = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Manage Students</h2>
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
                            <li><a href="students.php" class="active">Students</a></li>
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

            <h3><?php echo $edit_student ? 'Edit' : 'Add'; ?> Student</h3>
            <form method="post">
                <?php if ($edit_student): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="register_number">Register Number:</label>
                    <input type="text" id="register_number" name="register_number" value="<?php echo $edit_student ? $edit_student['register_number'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo $edit_student ? $edit_student['name'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="department_id">Department:</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo ($edit_student && $edit_student['department_id'] == $dept['id']) ? 'selected' : ''; ?>><?php echo $dept['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="number" id="year" name="year" min="1" max="3" value="<?php echo $edit_student ? $edit_student['year'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="semester">Semester:</label>
                    <input type="number" id="semester" name="semester" min="1" max="6" value="<?php echo $edit_student ? $edit_student['semester'] : ''; ?>" required>
                </div>
                <button type="submit" name="<?php echo $edit_student ? 'edit' : 'add'; ?>"><?php echo $edit_student ? 'Update' : 'Add'; ?> Student</button>
                <?php if ($edit_student): ?>
                    <a href="students.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>

            <h3>Students List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Register Number</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['register_number']; ?></td>
                            <td><?php echo $student['name']; ?></td>
                            <td><?php echo $student['department_name']; ?></td>
                            <td><?php echo $student['year']; ?></td>
                            <td><?php echo $student['semester']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $student['id']; ?>" class="btn">Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
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