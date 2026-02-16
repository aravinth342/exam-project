<?php
session_start();
require_once '../includes/functions.php';
requireLogin();

$report_type = isset($_GET['type']) ? $_GET['type'] : '';
$exam_id = isset($_GET['exam_id']) ? $_GET['exam_id'] : '';

if ($report_type && $exam_id) {
    // Generate report based on type
    if ($report_type == 'timetable') {
        $stmt = $pdo->prepare("SELECT e.*, s.name as subject_name, d.name as department_name FROM exams e JOIN subjects s ON e.subject_id = s.id JOIN departments d ON e.department_id = d.id WHERE e.id = ?");
        $stmt->execute([$exam_id]);
        $exam = $stmt->fetch();
        if ($exam) {
            echo "<!DOCTYPE html><html><head><title>Exam Timetable Report</title><link rel='stylesheet' href='../css/style.css'></head><body>";
            echo "<div class='container'>";
            echo "<header><h1>Chendhuran Polytechnic College</h1><p>Lenavilaku, Pudukkottai, Tamil Nadu</p><h2>Exam Timetable Report</h2></header>";
            echo "<main><h3>Exam Details</h3>";
            echo "<p><strong>Date:</strong> " . date('d-m-Y', strtotime($exam['date'])) . "</p>";
            echo "<p><strong>Subject:</strong> " . $exam['subject_name'] . "</p>";
            echo "<p><strong>Department:</strong> " . $exam['department_name'] . "</p>";
            echo "<button class='btn print-btn' onclick='window.print()'>Print Report</button>";
            echo "<table class='table'><thead><tr><th>Exam Date</th><th>Subject</th><th>Department</th><th>Session</th></tr></thead><tbody>";
            echo "<tr><td>" . date('d-m-Y', strtotime($exam['date'])) . "</td><td>" . $exam['subject_name'] . "</td><td>" . $exam['department_name'] . "</td><td>" . ucfirst($exam['session']) . "</td></tr>";
            echo "</tbody></table></main></div></body></html>";
            exit();
        }
    } elseif ($report_type == 'seating') {
        $stmt = $pdo->prepare("SELECT sa.*, e.date, s.name as subject_name, st.register_number, st.name as student_name, h.hall_no, d.name as department_name FROM seating_arrangements sa JOIN exams e ON sa.exam_id = e.id JOIN subjects s ON e.subject_id = s.id JOIN students st ON sa.student_id = st.id JOIN exam_halls h ON sa.hall_id = h.id JOIN departments d ON e.department_id = d.id WHERE sa.exam_id = ? ORDER BY sa.hall_id, sa.seat_number");
        $stmt->execute([$exam_id]);
        $arrangements = $stmt->fetchAll();
        if ($arrangements) {
            echo "<!DOCTYPE html><html><head><title>Hall-wise Seating Arrangement</title><link rel='stylesheet' href='../css/style.css'></head><body>";
            echo "<div class='container'>";
            echo "<header><h1>Chendhuran Polytechnic College</h1><p>Lenavilaku, Pudukkottai, Tamil Nadu</p><h2>Hall-wise Seating Arrangement</h2></header>";
            echo "<main><button class='btn print-btn' onclick='window.print()'>Print Report</button><br><br><h3>Exam: " . $arrangements[0]['subject_name'] . " - " . date('d-m-Y', strtotime($arrangements[0]['date'])) . "</h3>";
            echo "<table class='table'><thead><tr><th>Hall</th><th>Seat</th><th>Register Number</th><th>Student Name</th></tr></thead><tbody>";
            foreach ($arrangements as $arr) {
                echo "<tr><td>" . $arr['hall_no'] . "</td><td>" . $arr['seat_number'] . "</td><td>" . $arr['register_number'] . "</td><td>" . $arr['student_name'] . "</td></tr>";
            }
            echo "</tbody></table></main></div></body></html>";
            exit();
        }
    } elseif ($report_type == 'student') {
        $stmt = $pdo->prepare("SELECT sa.*, e.date, s.name as subject_name, st.register_number, st.name as student_name, h.hall_no, d.name as department_name FROM seating_arrangements sa JOIN exams e ON sa.exam_id = e.id JOIN subjects s ON e.subject_id = s.id JOIN students st ON sa.student_id = st.id JOIN exam_halls h ON sa.hall_id = h.id JOIN departments d ON e.department_id = d.id WHERE sa.exam_id = ? ORDER BY st.register_number");
        $stmt->execute([$exam_id]);
        $arrangements = $stmt->fetchAll();
        if ($arrangements) {
            echo "<!DOCTYPE html><html><head><title>Student-wise Seating List</title><link rel='stylesheet' href='../css/style.css'></head><body>";
            echo "<div class='container'>";
            echo "<header><h1>Chendhuran Polytechnic College</h1><p>Lenavilaku, Pudukkottai, Tamil Nadu</p><h2>Student-wise Seating List</h2></header>";
            echo "<main><button class='btn print-btn' onclick='window.print()'>Print Report</button><br><br><h3>Exam: " . $arrangements[0]['subject_name'] . " - " . date('d-m-Y', strtotime($arrangements[0]['date'])) . "</h3>";
            echo "<table class='table'><thead><tr><th>Register Number</th><th>Student Name</th><th>Hall</th><th>Seat</th></tr></thead><tbody>";
            foreach ($arrangements as $arr) {
                echo "<tr><td>" . $arr['register_number'] . "</td><td>" . $arr['student_name'] . "</td><td>" . $arr['hall_no'] . "</td><td>" . $arr['seat_number'] . "</td></tr>";
            }
            echo "</tbody></table></main></div></body></html>";
            exit();
        }
    } elseif ($report_type == 'department') {
        $stmt = $pdo->prepare("SELECT e.*, s.name as subject_name, d.name as department_name, COUNT(sa.id) as student_count FROM exams e JOIN subjects s ON e.subject_id = s.id JOIN departments d ON e.department_id = d.id LEFT JOIN seating_arrangements sa ON e.id = sa.exam_id WHERE e.id = ? GROUP BY e.id");
        $stmt->execute([$exam_id]);
        $exam = $stmt->fetch();
        if ($exam) {
            echo "<!DOCTYPE html><html><head><title>Department-wise Exam Report</title><link rel='stylesheet' href='../css/style.css'></head><body>";
            echo "<div class='container'>";
            echo "<header><h1>Chendhuran Polytechnic College</h1><p>Lenavilaku, Pudukkottai, Tamil Nadu</p><h2>Department-wise Exam Report</h2></header>";
            echo "<main><button class='btn print-btn' onclick='window.print()'>Print Report</button><br><br><h3>Exam Details</h3>";
            echo "<p><strong>Date:</strong> " . date('d-m-Y', strtotime($exam['date'])) . "</p>";
            echo "<p><strong>Subject:</strong> " . $exam['subject_name'] . "</p>";
            echo "<p><strong>Department:</strong> " . $exam['department_name'] . "</p>";
            echo "<p><strong>Session:</strong> " . ucfirst($exam['session']) . "</p>";
            echo "<p><strong>Number of Students:</strong> " . $exam['student_count'] . "</p>";
            echo "</main></div></body></html>";
            exit();
        }
    } elseif ($report_type == 'results') {
        $stmt = $pdo->prepare("SELECT r.*, e.date, s.name as subject_name, st.register_number, st.name as student_name, d.name as department_name FROM results r JOIN exams e ON r.exam_id = e.id JOIN subjects s ON e.subject_id = s.id JOIN students st ON r.student_id = st.id JOIN departments d ON st.department_id = d.id WHERE r.exam_id = ? ORDER BY st.register_number");
        $stmt->execute([$exam_id]);
        $results = $stmt->fetchAll();
        if ($results) {
            echo "<!DOCTYPE html><html><head><title>Exam Results Report</title><link rel='stylesheet' href='../css/style.css'></head><body>";
            echo "<div class='container'>";
            echo "<header><h1>Chendhuran Polytechnic College</h1><p>Lenavilaku, Pudukkottai, Tamil Nadu</p><h2>Exam Results Report</h2></header>";
            echo "<main><button class='btn print-btn' onclick='window.print()'>Print Report</button><br><br><h3>Exam: " . $results[0]['subject_name'] . " - " . date('d-m-Y', strtotime($results[0]['date'])) . "</h3>";
            echo "<table class='table'><thead><tr><th>Register Number</th><th>Student Name</th><th>Department</th><th>Marks Obtained</th><th>Total Marks</th><th>Grade</th><th>Status</th></tr></thead><tbody>";
            foreach ($results as $result) {
                echo "<tr><td>" . $result['register_number'] . "</td><td>" . $result['student_name'] . "</td><td>" . $result['department_name'] . "</td><td>" . $result['marks_obtained'] . "</td><td>" . $result['total_marks'] . "</td><td>" . $result['grade'] . "</td><td>" . ucfirst($result['status']) . "</td></tr>";
            }
            echo "</tbody></table></main></div></body></html>";
            exit();
        }
    }
}

$exams = getExams();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Exam Hall Arrangement System</title>
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
                    <h2>Exam Hall Arrangement System - Reports</h2>
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
                            <li><a href="reports.php" class="active">Reports</a></li>
                            <li><a href="announcements.php">Announcements</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <h3>Generate Reports</h3>
            <p>Select an exam and report type to generate:</p>

            <form method="get">
                <div class="form-group">
                    <label for="exam_id">Select Exam:</label>
                    <select id="exam_id" name="exam_id" required>
                        <option value="">Select Exam</option>
                        <?php foreach ($exams as $exam): ?>
                            <option value="<?php echo $exam['id']; ?>"><?php echo date('d-m-Y', strtotime($exam['date'])); ?> - <?php echo $exam['subject_name']; ?> (<?php echo $exam['department_name']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type">Report Type:</label>
                    <select id="type" name="type" required>
                        <option value="">Select Type</option>
                        <option value="timetable">Exam Timetable Report</option>
                        <option value="seating">Hall-wise Seating Arrangement</option>
                        <option value="student">Student-wise Seating List</option>
                        <option value="department">Department-wise Exam Report</option>
                        <option value="results">Exam Results Report</option>
                    </select>
                </div>
                <button type="submit">Generate Report</button>
            </form>
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