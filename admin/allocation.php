<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$message = '';

if (isset($_POST['allocate'])) {
    $exam_id = $_POST['exam_id'];

    // Get exam details
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([$exam_id]);
    $exam = $stmt->fetch();

    if ($exam) {
        // Get students from the department
        $stmt = $pdo->prepare("SELECT * FROM students WHERE department_id = ? ORDER BY register_number");
        $stmt->execute([$exam['department_id']]);
        $students = $stmt->fetchAll();

        // Get available halls
        $halls = getExamHalls();

        // Delete existing arrangements for this exam
        $stmt = $pdo->prepare("DELETE FROM seating_arrangements WHERE exam_id = ?");
        $stmt->execute([$exam_id]);

        $seat_number = 1;
        $hall_index = 0;

        foreach ($students as $student) {
            if ($hall_index >= count($halls)) {
                $message = "Not enough hall capacity for all students.";
                break;
            }

            $hall = $halls[$hall_index];

            // Insert seating arrangement
            $stmt = $pdo->prepare("INSERT INTO seating_arrangements (exam_id, student_id, hall_id, seat_number) VALUES (?, ?, ?, ?)");
            $stmt->execute([$exam_id, $student['id'], $hall['id'], $seat_number]);

            $seat_number++;

            // Move to next hall if current hall is full
            if ($seat_number > $hall['capacity']) {
                $seat_number = 1;
                $hall_index++;
            }
        }

        if (!$message) {
            $message = "Seating arrangements allocated successfully.";
        }
    } else {
        $message = "Exam not found.";
    }
}

$exams = getExams();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall Allocation - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Hall Allocation</h2>
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
                            <li><a href="allocation.php" class="active">Hall Allocation</a></li>
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

            <h3>Allocate Seats for Exam</h3>
            <form method="post">
                <div class="form-group">
                    <label for="exam_id">Select Exam:</label>
                    <select id="exam_id" name="exam_id" required>
                        <option value="">Select Exam</option>
                        <?php foreach ($exams as $exam): ?>
                            <option value="<?php echo $exam['id']; ?>"><?php echo date('d-m-Y', strtotime($exam['date'])); ?> - <?php echo $exam['subject_name']; ?> (<?php echo $exam['department_name']; ?> - <?php echo ucfirst($exam['session']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="allocate">Allocate Seats</button>
            </form>

            <h3>Current Seating Arrangements</h3>
            <?php
            $stmt = $pdo->query("SELECT sa.*, e.date, s.name as subject_name, st.register_number, st.name as student_name, h.hall_no, d.name as department_name FROM seating_arrangements sa JOIN exams e ON sa.exam_id = e.id JOIN subjects s ON e.subject_id = s.id JOIN students st ON sa.student_id = st.id JOIN exam_halls h ON sa.hall_id = h.id JOIN departments d ON e.department_id = d.id ORDER BY e.date, sa.hall_id, sa.seat_number");
            $arrangements = $stmt->fetchAll();

            if ($arrangements): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Exam Date</th>
                            <th>Subject</th>
                            <th>Department</th>
                            <th>Hall</th>
                            <th>Seat</th>
                            <th>Register Number</th>
                            <th>Student Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($arrangements as $arr): ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($arr['date'])); ?></td>
                                <td><?php echo $arr['subject_name']; ?></td>
                                <td><?php echo $arr['department_name']; ?></td>
                                <td><?php echo $arr['hall_no']; ?></td>
                                <td><?php echo $arr['seat_number']; ?></td>
                                <td><?php echo $arr['register_number']; ?></td>
                                <td><?php echo $arr['student_name']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No seating arrangements found.</p>
            <?php endif; ?>
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