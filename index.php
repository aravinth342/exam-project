<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: admin/dashboard.php');
        exit();
    } else {
        $error = "Invalid username or password";
    }
}

$student_results = null;
$student_register_number = '';
if (isset($_POST['check_results'])) {
    $register_number = sanitize($_POST['register_number']);
    $student_register_number = $register_number;
    $student_results = getStudentResults($register_number);
    if (empty($student_results)) {
        $results_error = "No results found for register number: " . $register_number;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Hall Arrangement System - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <img src="diulogoside.png" alt="Chendhuran Polytechnic College Logo" class="college-logo">
                <div class="college-info">
                    <h1>Chendhuran Polytechnic College</h1>
                    <p>Lenavilaku, Pudukkottai, Tamil Nadu</p>
                    <h2>Exam Hall Arrangement System</h2>
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
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="announcements.php">Announcements</a></li>
                    <li><a href="admin/login.php">Admin Login</a></li>
                </ul>
            </div>
        </nav>

        <main>
            <div class="login-form">
                <h3>Admin Login</h3>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login">Login</button>
                </form>
            </div>

            <div class="results-form">
                <h3>Check Your Results</h3>
                <?php if (isset($results_error)): ?>
                    <div class="error"><?php echo $results_error; ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="register_number">Register Number:</label>
                        <input type="text" id="register_number" name="register_number" required>
                    </div>
                    <button type="submit" name="check_results">Check Results</button>
                </form>

                <?php if ($student_results && is_array($student_results) && count($student_results) > 0): ?>
                    <h4>Your Results</h4>
                    <div class="student-info" style="margin-bottom: 20px;">
                        <p><strong>Register Number:</strong> <?php echo htmlspecialchars($student_register_number); ?></p>
                        <p><strong>Student Name:</strong> <?php echo htmlspecialchars($student_results[0]['student_name'] ?? 'N/A'); ?></p>
                        <p><strong>Department:</strong> <?php echo htmlspecialchars($student_results[0]['department_name'] ?? 'N/A'); ?></p>
                    </div>
                    <button class="btn print-btn" onclick="window.print()">Print Results</button>
                    <br><br>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Exam Date</th>
                                <th>Subject</th>
                                <th>Department</th>
                                <th>Marks Obtained</th>
                                <th>Total Marks</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($student_results as $result): ?>
                                <tr>
                                    <td><?php echo date('d-m-Y', strtotime($result['date'])); ?></td>
                                    <td><?php echo $result['subject_name']; ?></td>
                                    <td><?php echo $result['department_name']; ?></td>
                                    <td><?php echo $result['marks_obtained']; ?></td>
                                    <td><?php echo $result['total_marks']; ?></td>
                                    <td><?php echo $result['grade']; ?></td>
                                    <td><?php echo ucfirst($result['status']); ?></td>
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