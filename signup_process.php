<?php
// signup_process.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize user input
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
    $grad_year = htmlspecialchars($_POST['grad_year']);
    $company_name = htmlspecialchars($_POST['company_name']);
    $admission_number = htmlspecialchars($_POST['admission_number']);
    $Course_Name = htmlspecialchars($_POST['Course_Name']);
    
    // Connect to the database (replace with your actual database credentials)
    $conn = new mysqli('localhost', 'root', '', 'alumni_database');

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the Admission Number exists in the database
    $admission_check_sql = "SELECT * FROM valid_admissions WHERE admission_number = ?";
    $stmt = $conn->prepare($admission_check_sql);
    $stmt->bind_param("s", $admission_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Admission number exists, proceed with signup
        $insert_sql = "INSERT INTO alumni_users (full_name, email, password, grad_year, company_name, admission_number,Course_Name) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssssss", $full_name, $email, $password, $grad_year, $company_name, $admission_number,$Course_Name);

        if ($insert_stmt->execute()) {
            echo "Signup successful! Welcome to the Alumni Network.";
            // Redirect to login page or another page
            header("Location: login.html");
            exit;
        } else {
            echo "Error: " . $insert_stmt->error;
        }

        $insert_stmt->close();
    } else {
        // Admission number does not exist
        echo "Invalid Admission Number. Please try again.";
    }
    


    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
