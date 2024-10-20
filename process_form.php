<?php
// Connect to MySQL (replace these values with your actual database credentials)
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $student_id = $_POST['student_id'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $department_name = $_POST['department'];

    // Check if department exists
    $department_id = null;
    $dept_query = $conn->prepare("SELECT department_id FROM Department WHERE department_name = ?");
    $dept_query->bind_param("s", $department_name);
    $dept_query->execute();
    $dept_result = $dept_query->get_result();
    
    if ($dept_result->num_rows > 0) {
        $dept_row = $dept_result->fetch_assoc();
        $department_id = $dept_row['department_id'];
    } else {
        // If department doesn't exist, insert it
        $dept_insert = $conn->prepare("INSERT INTO Department (department_name) VALUES (?)");
        $dept_insert->bind_param("s", $department_name);
        if ($dept_insert->execute()) {
            $department_id = $conn->insert_id; // Get the newly inserted department ID
        } else {
            echo "Error inserting department: " . $dept_insert->error;
        }
        $dept_insert->close();
    }
    $dept_query->close();

    // Insert student details
    $stmt = $conn->prepare("INSERT INTO Student (name, student_id, dob, gender, address, mobile, email, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $student_id, $dob, $gender, $address, $mobile, $email, $department_name);
    
    if ($stmt->execute()) {
        echo "New record created successfully";
        // Insert into Mapping table
        $mapping_stmt = $conn->prepare("INSERT INTO Mapping (student_id, department_id) VALUES (?, ?)");
        $mapping_stmt->bind_param("si", $student_id, $department_id);
        
        if ($mapping_stmt->execute()) {
            echo "Mapping record created successfully";
        } else {
            echo "Error inserting mapping: " . $mapping_stmt->error;
        }
        $mapping_stmt->close();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Function to display students
function displayStudents($conn) {
    $sql = "SELECT * FROM Student";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "<h3>Student Records</h3><table border='1'>
                <tr><th>ID</th><th>Name</th><th>Student ID</th><th>DOB</th><th>Gender</th><th>Address</th><th>Mobile</th><th>Email</th><th>Department</th><th>Actions</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['student_id']}</td><td>{$row['dob']}</td><td>{$row['gender']}</td><td>{$row['address']}</td><td>{$row['mobile']}</td><td>{$row['email']}</td><td>{$row['department']}</td>
            <td><a href='?edit={$row['student_id']}'>Edit</a> | <a href='?delete={$row['student_id']}'>Delete</a></td></tr>";
        }
        echo "</table>";
    } else {
        echo "No records found.";
    }
}

// Handle update and delete actions
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM Student WHERE student_id = '$edit_id'";
    $edit_result = $conn->query($edit_sql);
    $edit_row = $edit_result->fetch_assoc();
    if ($edit_row) {
        // Fill form with existing data (could also be a separate edit form)
        echo "<form action='process_form.php' method='POST'>
                <input type='hidden' name='student_id' value='{$edit_row['student_id']}'>
                <label>Name:</label><input type='text' name='name' value='{$edit_row['name']}' required><br><br>
                <label>DOB:</label><input type='date' name='dob' value='{$edit_row['dob']}' required><br><br>
                <label>Gender:</label>
                <select name='gender' required>
                    <option value='Male'" . ($edit_row['gender'] == 'Male' ? ' selected' : '') . ">Male</option>
                    <option value='Female'" . ($edit_row['gender'] == 'Female' ? ' selected' : '') . ">Female</option>
                    <option value='Other'" . ($edit_row['gender'] == 'Other' ? ' selected' : '') . ">Other</option>
                </select><br><br>
                <label>Address:</label><textarea name='address' required>{$edit_row['address']}</textarea><br><br>
                <label>Mobile:</label><input type='tel' name='mobile' value='{$edit_row['mobile']}' required><br><br>
                <label>Email:</label><input type='email' name='email' value='{$edit_row['email']}' required><br><br>
                <label>Department:</label><input type='text' name='department' value='{$edit_row['department']}' required><br><br>
                <input type='submit' name='update' value='Update'>
              </form>";
    }
}

if (isset($_POST['update'])) {
    $student_id = $_POST['student_id'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $department = $_POST['department'];

    $stmt = $conn->prepare("UPDATE Student SET name=?, dob=?, gender=?, address=?, mobile=?, email=?, department=? WHERE student_id=?");
    $stmt->bind_param("ssssssss", $name, $dob, $gender, $address, $mobile, $email, $department, $student_id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    
    // First, delete from the Mapping table
    $delete_mapping_sql = "DELETE FROM Mapping WHERE student_id = ?";
    $mapping_stmt = $conn->prepare($delete_mapping_sql);
    $mapping_stmt->bind_param("s", $delete_id);
    
    if ($mapping_stmt->execute()) {
        // Then, delete from the Student table
        $delete_student_sql = "DELETE FROM Student WHERE student_id = ?";
        $student_stmt = $conn->prepare($delete_student_sql);
        $student_stmt->bind_param("s", $delete_id);
        
        if ($student_stmt->execute()) {
            echo "Record deleted successfully";
        } else {
            echo "Error deleting student: " . $student_stmt->error;
        }
        $student_stmt->close();
    } else {
        echo "Error deleting mapping: " . $mapping_stmt->error;
    }
    $mapping_stmt->close();
}

// Display all students
displayStudents($conn);

// Link to go back to the HTML form
echo '<h3><a href="student_form.html">Go back to Student Form</a></h3>';

// Close connection
$conn->close();
?>
