<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $hall_no = sanitize($_POST['hall_no']);
        $capacity = $_POST['capacity'];
        $stmt = $pdo->prepare("INSERT INTO exam_halls (hall_no, capacity) VALUES (?, ?)");
        $stmt->execute([$hall_no, $capacity]);
        $message = "Exam hall added successfully.";
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $hall_no = sanitize($_POST['hall_no']);
        $capacity = $_POST['capacity'];
        $stmt = $pdo->prepare("UPDATE exam_halls SET hall_no = ?, capacity = ? WHERE id = ?");
        $stmt->execute([$hall_no, $capacity, $id]);
        $message = "Exam hall updated successfully.";
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM exam_halls WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Exam hall deleted successfully.";
    }
}

$halls = getExamHalls();
$edit_hall = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM exam_halls WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_hall = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exam Halls - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Manage Exam Halls</h2>
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
                            <li><a href="halls.php" class="active">Exam Halls</a></li>
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

            <h3><?php echo $edit_hall ? 'Edit' : 'Add'; ?> Exam Hall</h3>
            <form method="post">
                <?php if ($edit_hall): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_hall['id']; ?>">
                <?php endif; ?>
                <div class="form-group">
                    <label for="hall_no">Hall Number:</label>
                    <input type="text" id="hall_no" name="hall_no" value="<?php echo $edit_hall ? $edit_hall['hall_no'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity:</label>
                    <input type="number" id="capacity" name="capacity" min="1" value="<?php echo $edit_hall ? $edit_hall['capacity'] : ''; ?>" required>
                </div>
                <button type="submit" name="<?php echo $edit_hall ? 'edit' : 'add'; ?>"><?php echo $edit_hall ? 'Update' : 'Add'; ?> Hall</button>
                <?php if ($edit_hall): ?>
                    <a href="halls.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>

            <h3>Exam Halls List</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hall Number</th>
                        <th>Capacity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($halls as $hall): ?>
                        <tr>
                            <td><?php echo $hall['id']; ?></td>
                            <td><?php echo $hall['hall_no']; ?></td>
                            <td><?php echo $hall['capacity']; ?></td>
                            <td>
                                <a href="?edit=<?php echo $hall['id']; ?>" class="btn">Edit</a>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $hall['id']; ?>">
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