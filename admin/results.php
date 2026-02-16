<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $exam_id = $_POST['exam_id'];
        $student_id = $_POST['student_id'];
        $marks_obtained = $_POST['marks_obtained'];
        $total_marks = $_POST['total_marks'];
        $grade = sanitize($_POST['grade']);
        $status = $_POST['status'];

        $stmt = $pdo->prepare("INSERT INTO results (exam_id, student_id, marks_obtained, total_marks, grade, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$exam_id, $student_id, $marks_obtained, $total_marks, $grade, $status]);
        $message = "Result added successfully.";
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $exam_id = $_POST['exam_id'];
        $student_id = $_POST['student_id'];
        $marks_obtained = $_POST['marks_obtained'];
        $total_marks = $_POST['total_marks'];
        $grade = sanitize($_POST['grade']);
        $status = $_POST['status'];

        $stmt = $pdo->prepare("UPDATE results SET exam_id = ?, student_id = ?, marks_obtained = ?, total_marks = ?, grade = ?, status = ? WHERE id = ?");
        $stmt->execute([$exam_id, $student_id, $marks_obtained, $total_marks, $grade, $status, $id]);
        $message = "Result updated successfully.";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM results WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Result deleted successfully.";
    }
}

$results = getResults();
$exams = getExams();
$students = getStudents();
$edit_result = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM results WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_result = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Manage Results</h2>
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
                            <li><a href="results.php" class="active">Results</a></li>
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

            <h3><?php echo $edit_result ? 'Edit' : 'Add'; ?> Result</h3>
            <form method="post">
                <?php if ($edit_result): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_result['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="exam_id">Exam:</label>
                    <select id="exam_id" name="exam_id" required>
                        <option value="">Select Exam</option>
                        <?php foreach ($exams as $exam): ?>
                            <option value="<?php echo $exam['id']; ?>" <?php echo ($edit_result && $edit_result['exam_id'] == $exam['id']) ? 'selected' : ''; ?>><?php echo date('d-m-Y', strtotime($exam['date'])); ?> - <?php echo $exam['subject_name']; ?> (<?php echo $exam['department_name']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="student_id">Student:</label>
                    <select id="student_id" name="student_id" required>
                        <option value="">Select Student</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['id']; ?>" <?php echo ($edit_result && $edit_result['student_id'] == $student['id']) ? 'selected' : ''; ?>><?php echo $student['register_number']; ?> - <?php echo $student['name']; ?> (<?php echo $student['department_name']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="marks_obtained">Marks Obtained:</label>
                    <input type="number" id="marks_obtained" name="marks_obtained" step="0.01" min="0" value="<?php echo $edit_result ? $edit_result['marks_obtained'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="total_marks">Total Marks:</label>
                    <input type="number" id="total_marks" name="total_marks" step="0.01" min="0" value="<?php echo $edit_result ? $edit_result['total_marks'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="grade">Grade:</label>
                    <input type="text" id="grade" name="grade" maxlength="5" value="<?php echo $edit_result ? $edit_result['grade'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="pass" <?php echo ($edit_result && $edit_result['status'] == 'pass') ? 'selected' : ''; ?>>Pass</option>
                        <option value="fail" <?php echo ($edit_result && $edit_result['status'] == 'fail') ? 'selected' : ''; ?>>Fail</option>
                        <option value="absent" <?php echo ($edit_result && $edit_result['status'] == 'absent') ? 'selected' : ''; ?>>Absent</option>
                    </select>
                </div>
                <button type="submit" name="<?php echo $edit_result ? 'edit' : 'add'; ?>"><?php echo $edit_result ? 'Update' : 'Add'; ?> Result</button>
                <?php if ($edit_result): ?>
                    <a href="results.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>

            <h3>Results List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Exam Date</th>
                        <th>Subject</th>
                        <th>Student</th>
                        <th>Register Number</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo date('d-m-Y', strtotime($result['date'])); ?></td>
                            <td><?php echo $result['subject_name']; ?></td>
                            <td><?php echo $result['student_name']; ?></td>
                            <td><?php echo $result['register_number']; ?></td>
                            <td><?php echo $result['marks_obtained']; ?>/<?php echo $result['total_marks']; ?></td>
                            <td><?php echo $result['grade']; ?></td>
                            <td><?php echo ucfirst($result['status']); ?></td>
                            <td>
                                <a href="?edit=<?php echo $result['id']; ?>" class="btn">Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $result['id']; ?>">
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