<?php
include '../conn.php'; // Adjust the path as needed

// Get the search term from the request
$searchTerm = isset($_GET['term']) ? mysqli_real_escape_string($conn, $_GET['term']) : '';

// Query to fetch products based on the search term
$query = "SELECT p.plantid, p.plantname, p.price, p.img1, u.email AS seller_email 
          FROM product p 
          JOIN users u ON p.added_by = u.id 
          WHERE p.plantname LIKE '%$searchTerm%'"; // Filter by plant name

$result = mysqli_query($conn, $query);
$plants = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($plant = mysqli_fetch_assoc($result)) {
        $plants[] = $plant; // Store each matching plant in the array
    }
}

// Return the matching plants as JSON
header('Content-Type: application/json');
echo json_encode( $plants);
?>