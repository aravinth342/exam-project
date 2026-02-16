<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $date = $_POST['date'];
        $subject_id = $_POST['subject_id'];
        $department_id = $_POST['department_id'];
        $session = $_POST['session'];
        $stmt = $pdo->prepare("INSERT INTO exams (date, subject_id, department_id, session) VALUES (?, ?, ?, ?)");
        $stmt->execute([$date, $subject_id, $department_id, $session]);
        $message = "Exam added successfully.";
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $date = $_POST['date'];
        $subject_id = $_POST['subject_id'];
        $department_id = $_POST['department_id'];
        $session = $_POST['session'];
        $stmt = $pdo->prepare("UPDATE exams SET date = ?, subject_id = ?, department_id = ?, session = ? WHERE id = ?");
        $stmt->execute([$date, $subject_id, $department_id, $session, $id]);
        $message = "Exam updated successfully.";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM exams WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Exam deleted successfully.";
    }
}

$exams = getExams();
$subjects = getSubjects();
$departments = getDepartments();
$edit_exam = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_exam = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Manage Exams</h2>
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
                            <li><a href="exams.php" class="active">Exams</a></li>
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

            <h3><?php echo $edit_exam ? 'Edit' : 'Add'; ?> Exam</h3>
            <form method="post">
                <?php if ($edit_exam): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_exam['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="date">Exam Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo $edit_exam ? $edit_exam['date'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="subject_id">Subject:</label>
                    <select id="subject_id" name="subject_id" required>
                        <option value="">Select Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>" <?php echo ($edit_exam && $edit_exam['subject_id'] == $subject['id']) ? 'selected' : ''; ?>><?php echo $subject['name']; ?> (<?php echo $subject['course_name']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="department_id">Department:</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo ($edit_exam && $edit_exam['department_id'] == $dept['id']) ? 'selected' : ''; ?>><?php echo $dept['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="session">Session:</label>
                    <select id="session" name="session" required>
                        <option value="morning" <?php echo ($edit_exam && $edit_exam['session'] == 'morning') ? 'selected' : ''; ?>>Morning</option>
                        <option value="afternoon" <?php echo ($edit_exam && $edit_exam['session'] == 'afternoon') ? 'selected' : ''; ?>>Afternoon</option>
                    </select>
                </div>
                <button type="submit" name="<?php echo $edit_exam ? 'edit' : 'add'; ?>"><?php echo $edit_exam ? 'Update' : 'Add'; ?> Exam</button>
                <?php if ($edit_exam): ?>
                    <a href="exams.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>

            <h3>Exams List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Subject</th>
                        <th>Department</th>
                        <th>Session</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                            <td><?php echo $exam['id']; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($exam['date'])); ?></td>
                            <td><?php echo $exam['subject_name']; ?></td>
                            <td><?php echo $exam['department_name']; ?></td>
                            <td><?php echo ucfirst($exam['session']); ?></td>
                            <td>
                                <a href="?edit=<?php echo $exam['id']; ?>" class="btn">Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $exam['id']; ?>">
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