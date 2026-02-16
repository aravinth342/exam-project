# Exam Hall Arrangement System

A complete LAMP stack web application for managing exam hall arrangements at Chendhuran Polytechnic College, Lenavilaku, Pudukkottai, Tamil Nadu.

## Features

- **Admin Module**: Secure login with session management
- **CRUD Operations**: Manage departments, courses, subjects, students, exam halls, exams, and results
- **Automatic Hall Allocation**: Assign students to exam halls based on capacity and department
- **Results Management**: Add and manage exam results for students
- **Student Portal**: Students can view their results using register number
- **Print Functionality**: Students can print their results with college logo and header
- **College Branding**: Official college logo displayed on all pages and printed reports
- **Report Generation**: Generate printable reports for timetables, seating arrangements, and student lists
- **Announcements System**: Public announcements page with downloadable time tables for exam dates
- **File Upload**: Admins can upload time table documents (PDF, DOC, DOCX) for exam announcements

## Technology Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Architecture**: LAMP (Linux, Apache, MySQL, PHP)

## Installation and Setup

### Prerequisites

- XAMPP (or any LAMP stack)
- Web browser

### Steps

1. **Install XAMPP**:
   - Download and install XAMPP from https://www.apachefriends.org/
   - Start Apache and MySQL services

2. **Clone/Download the Project**:
   - Place the project files in `C:\xampp\htdocs\exam_hall_system\` (or your XAMPP htdocs folder)

3. **Database Setup**:
   - Open phpMyAdmin (http://localhost/phpmyadmin/)
   - Create a new database named `exam_hall_system`
   - Import the `database.sql` file

4. **Configuration**:
   - Update database credentials in `includes/db.php` if necessary (default: root, no password)

5. **Access the Application**:
   - Open your browser and go to `http://localhost/exam_hall_system/`
   - Login with username: `admin`, password: `password`

## Folder Structure

```
exam_hall_system/
├── index.php              # Login page
├── diulogoside.png       # College logo
├── database.sql           # Database schema and sample data
├── README.md              # This file
├── includes/
│   ├── db.php            # Database connection
│   └── functions.php     # Common functions
├── admin/
│   ├── dashboard.php     # Admin dashboard
│   ├── departments.php   # Manage departments
│   ├── courses.php       # Manage courses
│   ├── subjects.php      # Manage subjects
│   ├── students.php      # Manage students
│   ├── halls.php         # Manage exam halls
│   ├── exams.php         # Manage exams
│   ├── allocation.php    # Hall allocation
│   ├── results.php       # Manage results
│   ├── reports.php       # Generate reports
│   └── logout.php        # Logout
├── css/
│   └── style.css         # Stylesheet
└── js/
    └── script.js         # JavaScript (if needed)
```

## Usage

1. **Login**: Use admin credentials to access the system
2. **Setup Data**: Add departments, courses, subjects, students, and exam halls
3. **Create Exams**: Schedule exams with dates, subjects, and departments
4. **Allocate Seats**: Automatically assign students to halls for each exam
5. **Add Results**: Enter exam results for students
6. **Student Access**: Students can check their results on the homepage using register number
7. **Print Results**: Students can print their results with college header using the print button
7. **Generate Reports**: Create printable reports for various purposes

## Security Features

- Session-based authentication
- Prepared statements to prevent SQL injection
- Input sanitization
- Secure password hashing

## Sample Data

The `database.sql` file includes sample data for testing:
- 5 departments (Mechanical, Electrical, etc.)
- Sample courses, subjects, students, and exam halls
- Default admin account: username `admin`, password `password`

## Reports

The system generates four types of reports:
1. **Exam Timetable Report**: Details of scheduled exams
2. **Hall-wise Seating Arrangement**: Students arranged by hall and seat
3. **Student-wise Seating List**: Seating information for each student
4. **Department-wise Exam Report**: Exam statistics by department

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Safari
- Edge

## Announcements Feature

The system includes a public announcements page where students and visitors can view important notices. For exam date announcements, admins must upload a single comprehensive PDF containing ALL examination time tables for all departments and subjects.

### Managing Announcements

1. **Admin Access**: Login to admin panel and navigate to "Announcements"
2. **Add Announcement**: Fill in title, type, content, and upload the comprehensive time table PDF for exam date announcements
3. **File Upload**: For exam date announcements, upload a single PDF file containing complete examination schedules for all departments
4. **Public View**: Students can access announcements at the main site and download the comprehensive time table PDF

### File Storage

- Uploaded files are stored in the `uploads/` directory with unique filenames
- Files are automatically deleted when announcements are removed
- Supported format: PDF only (comprehensive time table document)
- File naming: `comprehensive_timetable_[unique_id].pdf`

### Comprehensive Time Table PDF

The PDF should contain:
- Complete examination schedule for all departments
- Subject-wise time slots and dates
- Hall allocations and capacities
- Department-specific exam arrangements
- General examination instructions and rules

### Styled PDF Generation (Dompdf)

- A styled, logo-embedded PDF generator is available using Dompdf. To enable it, run:

```powershell
cd C:\xampp\htdocs\EXAM WW
composer install
```

- After installing dependencies, admins can use the **Auto-generate (Styled PDF)** button on the Announcements page to create a well-formatted PDF with college logo, table layout, and page numbers.

## Troubleshooting

- **Database Connection Error**: Check MySQL service is running and credentials in `db.php`
- **Permission Issues**: Ensure proper file permissions for XAMPP
- **Blank Page**: Check PHP error logs in XAMPP control panel

## License

This project is for educational purposes.

## Contact

For support or questions, please contact the development team.