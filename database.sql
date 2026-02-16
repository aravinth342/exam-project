-- Exam Hall Arrangement System Database
-- Chendhuran Polytechnic College

CREATE DATABASE IF NOT EXISTS exam_hall_system;
USE exam_hall_system;

-- Admin table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Departments table
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses table (Diploma programs)
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects table
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    course_id INT,
    semester INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    register_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    department_id INT,
    year INT NOT NULL,
    semester INT NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exam Halls table
CREATE TABLE exam_halls (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hall_no VARCHAR(20) UNIQUE NOT NULL,
    capacity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exams table
CREATE TABLE exams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL,
    subject_id INT,
    department_id INT,
    session ENUM('morning', 'afternoon') NOT NULL,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seating Arrangements table
CREATE TABLE seating_arrangements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    exam_id INT,
    student_id INT,
    hall_id INT,
    seat_number INT NOT NULL,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES exam_halls(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Results table
CREATE TABLE results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    exam_id INT,
    student_id INT,
    marks_obtained DECIMAL(5,2),
    total_marks DECIMAL(5,2),
    grade VARCHAR(5),
    status ENUM('pass', 'fail', 'absent') DEFAULT 'pass',
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Announcements table
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('exam_date', 'result', 'general', 'important') DEFAULT 'general',
    file_path VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- Insert sample admin
INSERT INTO admins (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Insert departments
INSERT INTO departments (name) VALUES
('Mechanical Engineering'),
('Electrical and Electronics Engineering'),
('Automobile Engineering'),
('Electronics and Communication Engineering'),
('Computer Engineering');

-- Insert courses (assuming one course per department for simplicity)
INSERT INTO courses (name, department_id) VALUES
('Diploma in Mechanical Engineering', 1),
('Diploma in Electrical and Electronics Engineering', 2),
('Diploma in Automobile Engineering', 3),
('Diploma in Electronics and Communication Engineering', 4),
('Diploma in Computer Engineering', 5);

-- Insert sample subjects
INSERT INTO subjects (name, course_id, semester) VALUES
('Engineering Mathematics', 1, 1),
('Engineering Physics', 1, 1),
('Engineering Drawing', 1, 2),
('Workshop Practice', 1, 2),
('Electrical Circuits', 2, 1),
('Digital Electronics', 2, 2),
('Automotive Engines', 3, 1),
('Vehicle Maintenance', 3, 2),
('Communication Systems', 4, 1),
('Microprocessors', 4, 2),
('Programming in C', 5, 1),
('Data Structures', 5, 2);

-- Insert sample students
INSERT INTO students (register_number, name, department_id, year, semester) VALUES
('MECH001', 'John Doe', 1, 1, 1),
('MECH002', 'Jane Smith', 1, 1, 1),
('EEE001', 'Bob Johnson', 2, 1, 1),
('EEE002', 'Alice Brown', 2, 1, 1),
('AUTO001', 'Charlie Wilson', 3, 1, 1),
('AUTO002', 'Diana Davis', 3, 1, 1),
('ECE001', 'Eve Miller', 4, 1, 1),
('ECE002', 'Frank Garcia', 4, 1, 1),
('CSE001', 'Grace Lee', 5, 1, 1),
('CSE002', 'Henry Taylor', 5, 1, 1);

-- Insert sample exam halls
INSERT INTO exam_halls (hall_no, capacity) VALUES
('Hall A', 50),
('Hall B', 50),
('Hall C', 50);

-- Insert sample exams
INSERT INTO exams (date, subject_id, department_id, session) VALUES
('2024-03-15', 1, 1, 'morning'),
('2024-03-15', 5, 2, 'afternoon'),
('2024-03-16', 9, 4, 'morning');

-- Insert sample results
INSERT INTO results (exam_id, student_id, marks_obtained, total_marks, grade, status) VALUES
(1, 1, 85.50, 100.00, 'A', 'pass'),
(1, 2, 78.25, 100.00, 'B', 'pass'),
(2, 3, 92.00, 100.00, 'A', 'pass'),
(2, 4, 65.75, 100.00, 'C', 'pass'),
(3, 7, 88.50, 100.00, 'A', 'pass'),
(3, 8, 71.25, 100.00, 'B', 'pass');

-- Insert sample announcements
INSERT INTO announcements (title, content, type, file_path, created_by) VALUES
('Semester Examination Schedule', 'The semester examinations for all departments will commence from March 15, 2024. Please download the comprehensive time table PDF below for complete examination schedules across all departments and subjects.', 'exam_date', 'comprehensive_timetable_sample.pdf', 1),
('Result Declaration', 'The results for the previous semester examinations have been declared. Students can check their results using their register number on the student portal.', 'result', NULL, 1),
('Important Notice: Exam Hall Rules', 'All students must carry their hall tickets and arrive 30 minutes before the exam time. Electronic devices are strictly prohibited in the examination halls.', 'important', NULL, 1),
('College Reopening', 'The college will reopen for the new academic year on June 1, 2024. All students are requested to complete their admission formalities before the due date.', 'general', NULL, 1);