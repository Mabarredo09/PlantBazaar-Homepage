<?php
// Include the database connection
include 'conn.php';

// Check if sellerId is set in the URL
if (isset($_GET['sellerId'])) {
    $sellerId = $_GET['sellerId'];

    // Fetch seller's profile data
    $sellerQuery = "SELECT u.firstname, u.lastname, u.email, u.proflepicture, u.address, s.ratings 
                    FROM users u 
                    JOIN sellers s ON u.id = s.user_id 
                    WHERE s.seller_id = ?";
    $sellerStmt = $conn->prepare($sellerQuery);
    $sellerStmt->bind_param("i", $sellerId);
    $sellerStmt->execute();
    $sellerResult = $sellerStmt->get_result();
    $sellerData = $sellerResult->fetch_assoc();

    if ($sellerData) {
        // Extract seller's profile data
        $sellerFirstname = $sellerData['firstname'];
        $sellerLastname = $sellerData['lastname'];
        $sellerEmail = $sellerData['email'];
        $sellerProfilePicture = $sellerData['proflepicture'];
        $sellerAddress = $sellerData['address'];
        $sellerRatings = $sellerData['ratings'];
    } else {
        echo "Seller not found.";
        exit;
    }

    // Fetch seller's listings
    $listingsQuery = "SELECT * FROM product WHERE added_by = ?";
    $listingsStmt = $conn->prepare($listingsQuery);
    $listingsStmt->bind_param("i", $sellerId);
    $listingsStmt->execute();
    $listingsResult = $listingsStmt->get_result();

} else {
    echo "No seller ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seller Profile - <?php echo htmlspecialchars($sellerFirstname . ' ' . $sellerLastname); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="seller-profile">
        <h2>Seller Profile</h2>
        <div class="seller-info">
            <img src="ProfilePictures/<?php echo htmlspecialchars($sellerProfilePicture); ?>" alt="Seller Profile Picture">
            <h3><?php echo htmlspecialchars($sellerFirstname . ' ' . $sellerLastname); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($sellerEmail); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($sellerAddress); ?></p>
            <p><strong>Ratings:</strong> <?php echo htmlspecialchars($sellerRatings); ?> / 5</p>
        </div>

        <div>
            <h3>Listings</h3>
            <?php
            if ($listingsResult->num_rows > 0) {
                echo '<ul class="listings">';
                while ($listing = $listingsResult->fetch_assoc()) {
                    echo '<li>';
                    echo '<h4>' . htmlspecialchars($listing['plantname']) . '</h4>';
                    echo '<p><strong>Price:</strong> ₱' . htmlspecialchars($listing['price']) . '</p>';
                    echo '<img src="Products/' . htmlspecialchars($sellerEmail) . '/' . htmlspecialchars($listing['img1']) . '" alt="' . htmlspecialchars($listing['plantname']) . '" style="width:100px;height:auto;">';
                    echo '<p><strong>Description:</strong> ' . htmlspecialchars($listing['details']) . '</p>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>No listings available.</p>';
            }
            ?>
        </div>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>