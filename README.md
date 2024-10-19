# Student Management System

This is a **PHP and MySQL-based Student Management System** that allows users to perform CRUD (Create, Read, Update, Delete) operations on student data. The project is designed to manage student records, department details, and the mapping of students to departments.

## Features
- Add new student records with details such as name, student ID, date of birth, gender, address, mobile number, email ID, and department.
- View a list of all student records.
- Update existing student details based on the student ID.
- Delete student records.
- Manage department details and map students to departments.

## Table of Contents
1. [Features](#features)
2. [Technologies Used](#technologies-used)
3. [Installation](#installation)
4. [Usage](#usage)
5. [Database Structure](#database-structure)
6. [Contributing](#contributing)
7. [License](#license)

## Technologies Used
- **PHP**: Server-side scripting language to handle form submissions and CRUD operations.
- **MySQL**: Database for storing student and department data.
- **HTML**: For creating the form interface.

## Installation

1. **Clone the repository**:
    ```bash
    git clone https://github.com/yourusername/student-management-system.git
    ```

2. **Navigate to the project directory**:
    ```bash
    cd student-management-system
    ```

3. **Set up the MySQL database**:
    - Create a new MySQL database (e.g., `student_management`).
    - The database and required tables will be automatically created when the form is submitted.

4. **Configure your database connection**:
    - Update the database connection details in the `process_form.php` file:
        ```php
        $servername = "localhost"; // Your database server
        $username = "root"; // Your database username
        $password = ""; // Your database password
        $dbname = "student_management"; // Your database name
        ```

5. **Run the project**:
    - Move the project files to your web server (e.g., `htdocs` if you're using XAMPP).
    - Start your Apache and MySQL server.
    - Open the project in your browser (e.g., `http://localhost/student-management-system/student_form.html`).

## Usage

1. Fill out the **Student Details Form** (`student_form.html`) to add new student records.
2. After submitting, student data will be saved to the database and displayed.
3. You can view all student records at the bottom of the page.
4. To update or delete records, extend the CRUD functionalities with your own logic (e.g., using hidden form fields or additional buttons).

## Database Structure

### Student Table
| Column Name  | Data Type | Description                      |
| ------------ | --------- | -------------------------------- |
| student_id   | VARCHAR(10) | Primary key, student identifier |
| name         | VARCHAR(100) | Student's full name            |
| dob          | DATE      | Date of birth                    |
| gender       | VARCHAR(10) | Student gender                  |
| address      | TEXT      | Address                          |
| mobile       | VARCHAR(15) | Mobile phone number             |
| email        | VARCHAR(100) | Email address                 |
| department   | VARCHAR(100) | Student's department           |

### Department Table
| Column Name    | Data Type   | Description                      |
| -------------- | ----------- | -------------------------------- |
| department_id  | INT         | Primary key, department ID        |
| department_name| VARCHAR(100) | Name of the department           |

### StudentDepartmentMapping Table
| Column Name   | Data Type   | Description                       |
| ------------- | ----------- | --------------------------------- |
| student_id    | VARCHAR(10) | Foreign key to `Student` table    |
| department_id | INT         | Foreign key to `Department` table |

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request or report any issues.

## License

This project is licensed under the Apache 2.0 License.
