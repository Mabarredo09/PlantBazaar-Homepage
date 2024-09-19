<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $gender = $_POST['gender'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];

    // Prepare the SQL statement with placeholders
    $sql = "INSERT INTO users (email, password, firstname, lastname, gender, phonenumber, address)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters ("sssssss" means seven strings)
        $stmt->bind_param("sssssss", $email, $password, $firstname, $lastname, $gender, $phonenumber, $address);
        
        // Execute the query
        if ($stmt->execute()) {
            echo 'success'; // Send success message back to the client
        } else {
            echo 'error'; // Send error message back to the client
        }

        // Close the statement
        $stmt->close();
    } else {
        echo 'error'; // Handle prepare() failure
    }
}
?>
