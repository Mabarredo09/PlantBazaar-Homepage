<?php
include '../conn.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("ss", $email, $password); // "ss" means two strings

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a user is found
    if ($result->num_rows > 0) {
        $_SESSION['email'] = $email;
        echo 'success'; // Send success message back to the client
    } else {
        echo 'error'; // Send error message back to the client
    }

    // Close the statement and connection
    $stmt->close();
}
?>
