<?php
include '../conn.php';
session_start();

// Check if a user is logged in
$isLoggedIn = isset($_SESSION['email']) && !empty($_SESSION['email']);

$profilePic = ''; // Placeholder for the profile picture
$isSeller = false; // Flag to check if the user is a seller

if ($isLoggedIn) {
    $email = $_SESSION['email'];

    // Query to get the profile picture from the database
    $query = "SELECT id, proflePicture, firstname, lastname FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $profilePic = $user['proflePicture'];  // Assuming you store the path to the profile picture
        $userId = $user['id'];
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
    }

    // If no profile picture is available, use a default image
    // if (empty($profilePic)) {
    //     $profilePic = 'plant-bazaar.jpg';  // Path to a default profile picture
    // }

    // Query to check if the user is a seller
    $sellerQuery = "SELECT seller_id FROM sellers WHERE user_id = '$userId'";
    $sellerResult = mysqli_query($conn, $sellerQuery);

    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $isSeller = true; // User is a seller
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="seller_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <title>Seller Dashboard</title>
</head>
<body>
<div class="header">
    <nav class="navigation">
        <div class="logo">
            <span class="plant">PLANT</span>
            <p class="bazaar">-BAZAAR</p>
            <i class="fa-solid fa-spa"></i>
        </div>
        <div class="nav1">
            <a href="../index.php">Home</a>
            <a href="#">About</a>
            <a href="#">Services</a>
            <a href="#">Contact</a>
        </div>
        <div class="login-signup">
            <?php if ($isLoggedIn): ?>
                <a href="#" class="profile-link">
                    <img src="../ProfilePictures/<?php echo $profilePic; ?>" alt="Profile" class="profile-pic">
                </a>
            <?php else: ?>
                <a href="#" id="loginLink">Login</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="hamburger">
        <i class="fas fa-bars"></i>
    </div>
</div>

<div class="dropdown-profile">
    <?php
    if ($isLoggedIn) {
        echo '<p>Hello, ' . $firstname . ' ' . $lastname . '</p>';
    }
    ?>
    <?php if ($isSeller): ?>
        <a href="Seller/seller_dashboard.php">Seller Dashboard</a>
    <?php else: ?>
        <a href="#">Be A Seller</a>
    <?php endif; ?>
    <a href="#">Edit Profile</a>
    <a href="#" id="logoutLink">Logout</a>
</div>

<div class="main-content">
    <h1>Welcome to Your Seller Dashboard</h1>
    <div class="product-list">
        <h2>Your Listed Plants</h2>
        <div class="card-container">
            <!-- Products will be dynamically inserted here -->
        </div>
    </div>
    <a href="add_product.php" class="add-product-btn">+ Add New Plant</a>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
   document.addEventListener('DOMContentLoaded', function() {
    // Check if the profile link exists (only when the user is logged in)
    const profileLink = document.querySelector('.profile-link');
    
    if (profileLink) {
        profileLink.addEventListener('click', function() {
            const dropdownMenu = document.querySelector('.dropdown-profile');
            if (dropdownMenu) {
                dropdownMenu.classList.toggle('show');
            } else {
                console.error("Dropdown menu not found");
            }
        });
    } else {
        console.log("Profile link not found - User may not be logged in");
    }

    // Hamburger menu functionality
    const hamburger = document.querySelector('.hamburger');
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            if (dropdownMenu) {
                dropdownMenu.classList.toggle('show');
            } else {
                console.error("Dropdown menu not found");
            }
        });
    } else {
        console.error("Hamburger menu not found");
    }
});

    $(document).ready(function() {
        fetchProducts(); // Fetch products on page load

function fetchProducts() {
    $.ajax({
        url: 'fetch_listed_plants.php',
        type: 'GET',
        dataType: 'json', // Specify the expected data type as JSON
        success: function(data) {
            console.log("Response from server:", data); // Log the response
            var productContainer = $('.card-container');
            productContainer.empty();

            data.forEach(function(product) {
            var card = $('<div>').addClass('card');
            card.append($('<h1>').text(product.plantname));
            card.append($('<img>').attr('src', '../Products/' + product.seller_email + '/' + product.img1).attr('alt', product.plantname));
            card.append($('<p>').text('Price: ₱' + product.price));
            card.append($('<p>').text('Category: ' + product.plantcategories));
            
            // Create a container for the buttons
            var buttonContainer = $('<div>').addClass('button-container');
            buttonContainer.append($('<a>').attr('href', 'edit_product.php?plantid=' + product.plantid).text('Edit Listing'));
            buttonContainer.append($('<a>').attr('href', 'delete_product.php?plantid=' + product.plantid).text('Delete Listing'));

    card.append(buttonContainer); // Append the button container to the card
    productContainer.append(card);
});

            if (data.length === 0) {
                productContainer.append($('<p>').text('You have no plants listed.'));
            }
        },
        error: function(xhr, status, error) {
            console.error("Request failed:", error);
        }
    });
}
    function loadAddProductForm() {
            $.ajax({
                url: 'add_product.php', // Load the add product form
                type: 'GET',
                success: function(data) {
                    $('.main-content').html(data); // Replace content with the add product form
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + status + error);
                }
            });
        }
});
    </script>
</body>
</html>