<?php
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors on the screen
// Include the database connection
include 'conn.php';

session_start();
$userId = $_SESSION['user_id'];

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
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="seller-profile">
    <div class="backBtn-container">
        <a href="#" class="back-btn" id="back">Back</a>
    </div>
        <h2>Seller Profile</h2>
        <div class="seller-info">
            <img src="ProfilePictures/<?php echo htmlspecialchars($sellerProfilePicture); ?>" alt="Seller Profile Picture">
            <h3><?php echo htmlspecialchars($sellerFirstname . ' ' . $sellerLastname); ?></h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($sellerEmail); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($sellerAddress); ?></p>
            <p><strong>Ratings:</strong> <?php echo htmlspecialchars($sellerRatings); ?> / 5</p>

            <div class="rate-seller">
                <h3>Rate this Seller</h3>
                <div class="star-rating">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <input type="hidden" name="sellerId" value="<?php echo htmlspecialchars($sellerId); ?>">
                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userId); ?>">
                <input type="hidden" name="rating" id="ratingValue" value="">
                <button type="button" id="submitRating">Submit Rating</button>
                <div class="rating-message"></div>
            </div>
        </div>
        <div>
    <h3>Listings</h3>
    <div class="plant-listings">
    <?php
    if ($listingsResult->num_rows > 0) {
        while ($listing = $listingsResult->fetch_assoc()) {
            echo '<div class="plant-card">';
            echo '<div class="plant-img-container">';
            echo '<img class="plant-img" src="Products/' . htmlspecialchars($sellerEmail) . '/' . htmlspecialchars($listing['img1']) . '" alt="' . htmlspecialchars($listing['plantname']) . '">';
            echo '</div>';
            echo '<div class="plant-details">';
            echo '<h4>' . htmlspecialchars($listing['plantname']) . '</h4>';
            echo '<p><strong>Price:</strong> ₱' . htmlspecialchars($listing['price']) . '</p>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($listing['details']) . '</p>';
            // Modify the View More Details button to redirect to viewmoredetails.php
            echo '<button class="view-details" onclick="viewMoreDetails(' . htmlspecialchars($listing['plantid']) . ', \'' . htmlspecialchars($sellerEmail) . '\')">View more details</button>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No listings available.</p>';
    }
    ?>
</div>

<!-- Zoom Modal for Plant Image -->
<div id="zoom-plant-modal">
    <span class="close">&times;</span>
    <img id="zoomed-plant-img" src="" alt="Plant Image">
</div>

<!-- Zoom Modal -->
<div id="zoom-modal">
    <span class="close">&times;</span>
    <img id="zoomed-img" src="" alt="Profile Picture">
</div>

<script>

     // Star rating functionality
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('mouseover', function() {
        // Highlight all stars up to the hovered star
        const ratingValue = this.getAttribute('data-value');

        // Add the 'selected' class to stars up to the hovered star
        document.querySelectorAll('.star').forEach(s => {
            s.classList.remove('selected'); // Reset all stars
            if (parseInt(s.getAttribute('data-value')) <= ratingValue) {
                s.classList.add('selected'); // Highlight stars up to the hovered one
            }
        });
    });

    star.addEventListener('mouseout', function() {
        // Remove highlighted stars when not hovering
        document.querySelectorAll('.star').forEach(s => {
            s.classList.remove('selected'); // Reset all stars
        });

        // Keep the selected stars if they were clicked
        const currentRating = document.getElementById('ratingValue').value;
        if (currentRating) {
            document.querySelectorAll('.star').forEach(s => {
                if (parseInt(s.getAttribute('data-value')) <= currentRating) {
                    s.classList.add('selected');
                }
            });
        }
    });

    star.addEventListener('click', function() {
        const ratingValue = this.getAttribute('data-value');
        document.getElementById('ratingValue').value = ratingValue;

        // Remove 'selected' class from all stars
        document.querySelectorAll('.star').forEach(s => s.classList.remove('selected'));
        
        // Add 'selected' class to the clicked star and all stars before it
        this.classList.add('selected');
        let previousStar = this.previousElementSibling;
        
        while (previousStar) {
            previousStar.classList.add('selected');
            previousStar = previousStar.previousElementSibling;
        }
    });
});

    // Submit rating functionality
    document.getElementById('submitRating').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent any default action

        const formData = new FormData(); // Create a new FormData object
        formData.append('sellerId', document.querySelector('input[name="sellerId"]').value);
        formData.append('userId', document.querySelector('input[name="userId"]').value);
        formData.append('rating', document.getElementById('ratingValue').value);

        // Perform AJAX request to submit rating
        fetch('Ajax/rateSeller.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            // Update the UI based on the response
            if (data.success) {
                document.querySelector('.rating-message').textContent = 'Thank you for your rating! Average rating: ' + data.average_rating + ' / 5';
                // Reset the rating stars
                
            } else {
                document.querySelector('.rating-message').textContent = 'Failed to submit rating: ' + data.message;
                // Reset the rating stars
                document.querySelectorAll('.star').forEach(s => s.classList.remove('selected'));
                alert('Failed to submit rating: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.querySelector('.rating-message').textContent = 'An error occurred. Please try again later.';
        });
    });

     // Get elements for profile image zoom
    const profileImg = document.querySelector('.seller-info img');
    const modal = document.getElementById('zoom-modal');
    const zoomedImg = document.getElementById('zoomed-img');
    const closeModal = document.querySelector('#zoom-modal .close');
    const back = document.getElementById('back');

    // Open modal when profile image is clicked
    profileImg.addEventListener('click', function() {
        zoomedImg.src = profileImg.src;
        modal.style.display = 'block';
    });

    // Close modal when 'x' is clicked
    closeModal.addEventListener('click', function() {
        window.history.back();
    });

    // Close modal when clicking outside the image
    modal.addEventListener('click', function(e) {
        if (e.target !== zoomedImg) {
            modal.style.display = 'none';
        }
    });

    // Add event listeners for plant images
    document.querySelectorAll('.plant-img').forEach(img => {
        img.addEventListener('click', function() {
            const zoomedPlantImg = document.getElementById('zoomed-plant-img');
            zoomedPlantImg.src = img.src;
            document.getElementById('zoom-plant-modal').style.display = 'block';
        });
    });

    // Close zoom modal for plant image
    document.querySelector('#zoom-plant-modal .close').addEventListener('click', function() {
        document.getElementById('zoom-plant-modal').style.display = 'none';
    });

    // Close zoom modal when clicking outside the image
    document.getElementById('zoom-plant-modal').addEventListener('click', function(e) {
        if (e.target !== document.getElementById('zoomed-plant-img')) {
            document.getElementById('zoom-plant-modal').style.display = 'none';
        }
    });

    // Add event listeners for back button
    back.addEventListener('click', function() {
        window.location.href = 'index';
    });

    // Function to handle viewing more details
    function viewMoreDetails(plantId, sellerEmail) {
        // Redirect to viewmoredetails.php with plantId and sellerEmail
        window.location.href = 'viewmoredetails.php?plantId=' + plantId + '&sellerEmail=' + encodeURIComponent(sellerEmail);
    }
</script>

</body>
</html>
