<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "final_project"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jobTitle = $_POST['job_title'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // File upload handling
    $uploadDir = 'resume/'; // Directory to store resume files
    $uploadFile = $uploadDir . basename($_FILES['resume']['name']);
    
    // Move uploaded file to specified directory
    if (move_uploaded_file($_FILES['resume']['tmp_name'], $uploadFile)) {
        echo '<script>alert("Application submitted successfully.");</script>';
    } else {
        echo '<script>alert("Error uploading resume file.");</script>';
    }

    // SQL to insert data into database
    $resumePath = $uploadDir . $_FILES['resume']['name'];
    $sql = "INSERT INTO job_applications (job_title, name, email, resume) VALUES ('$jobTitle', '$name', '$email', '$resumePath')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to home.php after successful upload and insertion
        echo '<script>window.location.href = "home.php";</script>';
        exit(); // Make sure to exit after redirection
    } else {
        echo '<script>alert("Error inserting data into database.");</script>';
    }
}

$conn->close();
?>
