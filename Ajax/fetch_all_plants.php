<?php
include '../conn.php'; // Adjust the path as needed

// Query to fetch all products
$query = "SELECT p.plantid, p.plantname, p.price, p.img1, u.email AS seller_email 
          FROM product p 
          JOIN users u ON p.added_by = u.id"; // Fetch all products

$result = mysqli_query($conn, $query);
$plants = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($plant = mysqli_fetch_assoc($result)) {
        $plants[] = $plant; // Store each plant in the array
    }
}

// Return the plants as JSON
header('Content-Type: application/json');
echo json_encode($plants);
?>