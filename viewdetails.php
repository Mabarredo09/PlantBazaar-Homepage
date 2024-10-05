<?php
// Include the database connection
include 'conn.php';

// Check if plantId and sellerEmail are set in the URL
if (isset($_POST['plantId']) && isset($_POST['sellerEmail'])) {
    $plantId = $_POST['plantId'];
    $sellerEmail = $_POST['sellerEmail'];

    // Fetch plant details from the database
    $sql = "SELECT * FROM product WHERE plantid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $plantId);
    $stmt->execute();
    $result = $stmt->get_result();
    $plant = $result->fetch_assoc();

    if ($plant) {
        // Extract plant data
        $sellerId = $plant['added_by'];
        
        $plantName = $plant['plantname'];
        $plantDescription = $plant['details'];
        $plantPrice = $plant['price'];
        $plantLocation = $plant['location'];
        $plantColor = $plant['plantColor'];
        $plantSize = $plant['plantSize'];
        $plantCategories = $plant['plantcategories'];
        $img1 = $plant['img1'];
        $img2 = $plant['img2'];
        $img3 = $plant['img3'];

        // Fetch seller's profile data
        $sellerQuery = "SELECT u.firstname, u.lastname, u.email, u.proflepicture, u.address, s.ratings FROM users u JOIN sellers s ON u.id = s.user_id WHERE s.seller_id = ?";
        $sellerStmt = $conn->prepare($sellerQuery);
        $sellerStmt->bind_param("i", $sellerId);
        $sellerStmt->execute();
        $sellerResult = $sellerStmt->get_result();
        $sellerData = $sellerResult->fetch_assoc();

        if ($sellerData) {
            // Extract seller's profile data
            // print_r($sellerData); // This will output the entire array for inspection
            // // Check if ratings exists
            // if (array_key_exists('ratings', $sellerData)) {
            //     $sellerRatings = $sellerData['ratings'];
            // } else {
            //     echo "Ratings data not found.";
            // }

            $sellerFirstname = $sellerData['firstname'];
            $sellerLastname = $sellerData['lastname'];
            $sellerEmail = $sellerData['email'];
            $sellerProfilePicture = $sellerData['proflepicture'];
            $sellerAddress = $sellerData['address'];
            $sellerRatings = $sellerData['ratings'];
        } else {
            echo "No data found for seller ID: " . $sellerId;
            exit;
        }

    } else {
        echo "Plant not found.";
        exit;
    }
} else {
    echo "Invalid plant ID or seller email.";
    exit;
}


// Function to get the valid image path
function getImagePath($sellerEmail, $img) {
    $path = "Products/$sellerEmail/$img";
    // Check if the image exists and is not default or empty
    if (!empty($img) && file_exists($path) && $img !== 'default-image.jpg') {
        return $path; // Return the valid image path
    }
    return "default-image.jpg"; // Fallback to default image
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Details - <?php echo $plantName; ?></title>
    <link rel="stylesheet" href="viewdetails.css">
</head>
<body>
    <!-- X button on the left to redirect to index.php -->
    <a href="index" class="close-card">&times;</a>
<div class="container">
    <div class="plantContainer">
    <div class="card">
        <div class="card-image-container">
        <img id="plant-image" src="<?php echo getImagePath($sellerEmail, $img1); ?>" alt="<?php echo $plantName; ?>">

        <div class="card-image-controls">
            <button id="prev-btn"><</button>
            <button id="next-btn">></button>
        </div>
        </div>
        <div class="card-content">
            <h1><?php echo $plantName; ?></h1>
            <div class="plant-details">
                <p><strong>Price:</strong> ₱<?php echo $plantPrice; ?></p>
                <p><strong>Location:</strong> <?php echo $plantLocation; ?></p>
                <p><strong>Color:</strong> <?php echo $plantColor; ?></p>
                <p><strong>Size:</strong> <?php echo $plantSize; ?></p>
                <p><strong>Categories:</strong> <?php echo $plantCategories; ?></p>
                <p><strong>Details:</strong> <?php echo $plantDescription; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="profilerContainer">
    <!-- Seller Profile Section -->
        <div class="seller-profile">
        <h2>Seller Profile</h2>
        <div class="seller-info">
            <img src="ProfilePictures/<?php echo $sellerProfilePicture; ?>" alt="Seller Profile Picture">
            <h3><?php echo $sellerFirstname . ' ' . $sellerLastname; ?></h3>
            <p>Email: <?php echo $sellerData['email']; ?></p>
            <p>Location: <?php echo $sellerData['address']; ?></p>
            <p>Rating: <?php echo $sellerRatings;?> / 5</p>
        </div>
        <form action="profile.php" method="get">
        <input type="hidden" name="sellerId" value="<?php echo $sellerId; ?>">
        <button type="submit">View Seller Profile</button>
</form>
</div>
</div>
    <!-- Modal for Image Zoom -->
    <div id="imageModal" class="modal">
        <span class="close-modal" id="closeModal">&times;</span>
        <img class="modal-content" id="zoomed-image">
    </div>

    <script>
        // Array of image paths  
        let images = [
        '<?php echo getImagePath($sellerEmail, $img1); ?>',
        '<?php echo getImagePath($sellerEmail, $img2); ?>',
        '<?php echo getImagePath($sellerEmail, $img3); ?>'
    ];
      

        let currentImageIndex = 0;

        // Select the image element and buttons
        const plantImage = document.getElementById('plant-image');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const modal = document.getElementById('imageModal');
        const zoomedImage = document.getElementById('zoomed-image');
        const closeModal = document.getElementById('closeModal');

        // Zoom in on the image when clicked
        plantImage.addEventListener('click', function() {
            modal.style.display = "block";
            zoomedImage.src = plantImage.src;
        });

        // Close the modal without redirecting
        closeModal.addEventListener('click', function() {
            modal.style.display = "none";
        });

       // Event listener for the Previous button
            prevBtn.addEventListener('click', function() {
                currentImageIndex--;
                if (currentImageIndex < 0) {
                    currentImageIndex = images.length - 1;
                }
                const imageUrl = images[currentImageIndex];
                if (imageUrl === 'default-image.jpg') {
                    prevBtn.click(); // Simulate a click on the previous button to loop back to other images
                } else {
                    plantImage.src = imageUrl;
                }
            });

            // Event listener for the Next button
            nextBtn.addEventListener('click', function() {
                currentImageIndex++;
                if (currentImageIndex >= images.length) {
                    currentImageIndex = 0;
                }
                const imageUrl = images[currentImageIndex];
                if (imageUrl === 'default-image.jpg') {
                    nextBtn.click(); // Simulate a click on the next button to loop back to other images
                } else {
                    plantImage.src = imageUrl;
                }
            });

            // Error handling for image loading
            plantImage.addEventListener('error', function() {
                console.error('Error loading image:', plantImage.src);
                // You can handle the error here, e.g. display a default image or show an error message
            });


        // Close the modal when clicking outside of the image
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>