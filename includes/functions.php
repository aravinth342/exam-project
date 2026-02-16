<?php
require_once 'db.php';

// Function to check if admin is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit();
    }
}

// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to get all departments
function getDepartments() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM departments ORDER BY name");
    return $stmt->fetchAll();
}

// Function to get all courses
function getCourses() {
    global $pdo;
    $stmt = $pdo->query("SELECT c.*, d.name as department_name FROM courses c JOIN departments d ON c.department_id = d.id ORDER BY c.name");
    return $stmt->fetchAll();
}

// Function to get all subjects
function getSubjects() {
    global $pdo;
    $stmt = $pdo->query("SELECT s.*, c.name as course_name FROM subjects s JOIN courses c ON s.course_id = c.id ORDER BY s.name");
    return $stmt->fetchAll();
}

// Function to get all students
function getStudents() {
    global $pdo;
    $stmt = $pdo->query("SELECT s.*, d.name as department_name FROM students s JOIN departments d ON s.department_id = d.id ORDER BY s.register_number");
    return $stmt->fetchAll();
}

// Function to get all exam halls
function getExamHalls() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM exam_halls ORDER BY hall_no");
    return $stmt->fetchAll();
}

// Function to get all exams
function getExams() {
    global $pdo;
    $stmt = $pdo->query("SELECT e.*, s.name as subject_name, d.name as department_name FROM exams e JOIN subjects s ON e.subject_id = s.id JOIN departments d ON e.department_id = d.id ORDER BY e.date, e.session");
    return $stmt->fetchAll();
}

// Function to get all results
function getResults() {
    global $pdo;
    $stmt = $pdo->query("SELECT r.*, e.date, s.name as subject_name, st.register_number, st.name as student_name, d.name as department_name FROM results r JOIN exams e ON r.exam_id = e.id JOIN subjects s ON e.subject_id = s.id JOIN students st ON r.student_id = st.id JOIN departments d ON st.department_id = d.id ORDER BY e.date, st.register_number");
    return $stmt->fetchAll();
}

// Function to get student results by register number
function getStudentResults($register_number) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT r.*, e.date, s.name as subject_name, st.name as student_name, d.name as department_name FROM results r JOIN exams e ON r.exam_id = e.id JOIN subjects s ON e.subject_id = s.id JOIN students st ON r.student_id = st.id JOIN departments d ON st.department_id = d.id WHERE st.register_number = ? ORDER BY e.date");
    $stmt->execute([$register_number]);
    return $stmt->fetchAll();
}

// Function to get all announcements
function getAnnouncements() {
    global $pdo;
    $stmt = $pdo->query("SELECT a.*, ad.username as created_by_name FROM announcements a LEFT JOIN admins ad ON a.created_by = ad.id ORDER BY a.created_at DESC");
    return $stmt->fetchAll();
}
?>