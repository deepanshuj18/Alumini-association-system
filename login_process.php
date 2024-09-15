<?php
// login_process.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize user input
    $admission_number = htmlspecialchars($_POST['admission_number']);
    $password = htmlspecialchars($_POST['password']);

    // Connect to the database (replace with your actual database credentials)
    $conn = new mysqli('127.0.0.1', 'root', '', 'alumni_database');

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the Admission Number exists and retrieve the corresponding password hash
    $sql = "SELECT id, password FROM alumni_users WHERE admission_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admission_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, log the user in
            $_SESSION['user_id'] = $row['id'];
            echo "Login successful! Redirecting...";
            header("Location: dashboard.php");
            exit;
        } else {
            // Incorrect password
            echo "Incorrect password. Please try again.";
        }
    } else {
        // Admission number not found
        echo "Admission Number not found. Please try again.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
