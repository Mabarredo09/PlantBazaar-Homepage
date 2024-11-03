<?php
include 'conn.php';
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
    $sellerQuery = "SELECT seller_id, user_id FROM sellers WHERE user_id = '$userId'";
    $sellerResult = mysqli_query($conn, $sellerQuery);

    if ($sellerResult && mysqli_num_rows($sellerResult) > 0) {
        $isSeller = true; // User is a seller

        $sellerData = mysqli_fetch_assoc($sellerResult);
        $sellerUserId = $sellerData['user_id'];
    }
    if(!$sellerResult){
        $sellerUserId = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/css/splide.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4/dist/js/splide.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    
    <title>Plant-Bazaar</title>
</head>
    <div class="header">
        <nav class="navigation">
            <div class="logo">
                <span class="plant">PLANT</span>
                <p class="bazaar">-BAZAAR</p>
                <i class="fa-solid fa-spa"></i>
            </div>
            <div class="nav1">
                <a href="#" id="home">Home</a>
                <a href="categories">Plants Categories</a>
                <a href="#" id="about">About</a>
                <a href="#">Contact Us</a>
                <?php if ($isLoggedIn): ?>
                <a href="#" id="chats">Chats</a>
                <?php endif;?>
            </div>
            <div class="login-signup">
                <?php if ($isLoggedIn): ?>
                    <!-- Show Profile Picture if user is logged in -->
                    <a href="#" class="profile-link">
                        <img src="ProfilePictures/<?php echo $profilePic; ?>" alt="Profile" class="profile-pic">
                    </a>
                    
                <?php else: ?>
                    <!-- Show Login button if user is not logged in -->
                    <a href="#" id="loginLink">Login</a>
                <?php endif; ?>
            </div>
        </nav>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
    </div>

    <div class="dropdown-menu">
        <?php if ($isLoggedIn): ?>
            <a href="#" class="profile-link">
                        <img src="ProfilePictures/<?php echo $profilePic; ?>" alt="Profile" class="profile-pic">
            </a>
            <a><p>Hello, <?php echo $firstname . ' ' . $lastname; ?></p> </a>
        <?php endif;?>
        <a href="#" id="home1">Home</a>
        <a href="#" id="about1">About</a>
        <a href="#">Contact</a>
        <?php if ($isLoggedIn): ?>
            <a href="#" id="logoutLink">Logout</a>
        <?php else:?>
        <a href="#" id="loginLink1">Login</a>
        <?php endif;?>
    </div>

    <div class="dropdown-profile">
   <?php
    if ($isLoggedIn) {
        echo'<p>Hello, ' . $firstname . ' ' . $lastname . '</p>';
    }?>
    <?php if ($isSeller): ?>
        <a href="Seller/seller_dashboard">Seller Dashboard</a> <!-- Change the link as needed for the seller's dashboard -->
    <?php else: ?>
        <a href="#">Be A Seller</a> <!-- Link to becoming a seller -->
    <?php endif; ?>
    <a href="#">Edit Profile</a>
    <a href="#" id="logoutLink">Logout</a>
</div>

<body>
    <div class="container">
        <h1>Plant Categories</h1>
        <div class="filterButton">
            <input type="text" id="search-input" placeholder="Search for plants" />
        </div>
        <div class="newly-contents" id="plants-categories">
            <!-- Dynamic category items will be injected here -->
            <?php
            // Fetch all products to display initially
            $plantQuery = "SELECT p.plantid, p.plantname, p.price, p.img1, u.email AS seller_email 
                           FROM product p 
                           JOIN users u ON p.added_by = u.id"; // Fetch all products
            $plantResult = mysqli_query($conn, $plantQuery);
            if ($plantResult && mysqli_num_rows($plantResult) > 0) {
                while ($plant = mysqli_fetch_assoc($plantResult)) {
                    echo '<div class="plant-item">' .
                            '<div class="plant-image">' .
                                '<img src="Products/' . htmlspecialchars($plant['seller_email']) . '/' . htmlspecialchars($plant['img1']) . '" alt="' . htmlspecialchars($plant['plantname']) . '">' .
                            '</div>' .
                            '<p>' . htmlspecialchars($plant['plantname']) . '</p>' .
                            '<p>Price: ₱' . htmlspecialchars($plant['price']) . '</p>' .
                            '<div class="plant-item-buttons">' .
                                '<button class="view-details" data-id="' . htmlspecialchars($plant['plantid']) . '" data-email="' . htmlspecialchars($plant['seller_email']) . '">View Details</button>' .
                            '</div>' .
                        '</div>';
                }
            } else {
                echo '<p>No plants available.</p>';
            }
            ?>
        </div>
    </div>
</body>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-links">
            <h3>Quick Links</h3>
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Services</a>
            <a href="">Contact Us</a>
            <a href="#">Privacy Policy</a>
        </div>
        <div class="footer-contact">
            <h3>Contact Us</h3>
            <p>Email: support@plantbazaar.com</p>
            <p>Phone: +123 456 7890</p>
        </div>
        <div class="footer-social">
            <h3>Follow Us</h3>
            <a href="#" class="social-icon"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2023 Plant Bazaar. All rights reserved.</p>
    </div>
</footer>
<script>


var isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
var userId = <?php echo $isLoggedIn ? json_encode($userId) : 'null'; ?>; // Pass userId if logged in
var sellerUserId = <?php echo $isSeller ? json_encode($sellerUserId) : 'null'; ?>;

$(document).ready(function() {
     // Function to fetch all plants
     function fetchAllPlants() {
        $.ajax({
            url: 'Ajax/fetch_all_plants.php', // Create this new endpoint
            type: 'GET',
            success: function(response) {
                $('#plants-categories').empty(); // Clear previous results

                if (response.length > 0) {
                    // Loop through the results and append them to the category list
                    response.forEach(function(plant) {
                        $('#plants-categories').append(
                            '<div class="plant-item">' +
                                '<div class="plant-image">' +
                                    '<img src="Products/' + plant.seller_email + '/' + plant.img1 + '" alt="' + plant.plantname + '">' +
                                '</div>' +
                                '<p>' + plant.plantname + '</p>' +
                                '<p>Price: ₱' + plant.price + '</p>' +
                                '<div class="plant-item-buttons">' +
                                    '<button class="view-details" data-id="' + plant.plantid + '" data-email="' + plant.seller_email + '">View Details</button>' +
                                '</div>' +
                            '</div>'
                        );
                    });
                } else {
                    $('#plants-categories').append('<p>No plants available.</p>'); // No results message
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    }

    $('#search-input').on('keyup', function() {
        var searchTerm = $(this).val(); // Get the search input value
        console.log("Search Term:", searchTerm); // Log the search term
        if (searchTerm.trim() === ' ') {
            // If the search term is empty, fetch all plants again
            fetchAllPlants();
        } else {
            // If there is a search term, perform the search
            $.ajax({
                url: 'Ajax/search_plants.php', // Endpoint for searching
                type: 'GET',
                data: { term: searchTerm }, // Send the search term to the server
                success: function(response) {
                    console.log("Search Response:", response); // Log the response for debugging
                    $('#plants-categories').empty(); // Clear previous results

                    if (response.length > 0) {
                        // Loop through the results and append them to the category list
                        response.forEach(function(plant) {
                            $('#plants-categories').append(
                                '<div class="plant-item">' +
                                    '<div class="plant-image">' +
                                        '<img src="Products/' + plant.seller_email + '/' + plant.img1 + '" alt="' + plant.plantname + '">' +
                                    '</div>' +
                                    '<p>' + plant.plantname + '</p>' +
                                    '<p>Price: ₱' + plant.price + '</p>' +
                                    '<div class="plant-item-buttons">' +
                                        '<button class="view-details" data-id="' + plant.plantid + '" data-email="' + plant.seller_email + '">View Details</button>' +
                                    '</div>' +
                                '</div>'
                            );
                        });
                    } else {
                        $('#plants-categories').append('<p>No plants found.</p>'); // No results message
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        }
    });

   
});
    // AJAX Fetching of newly listed plants
$.ajax({ 
    url: 'Ajax/fetch_newly_listed.php',
    type: 'GET',
    success: function(response) {
        try {
            let plants = response;

            if (plants.error) {
                alert(plants.error); // Show error message if any
                return;
            }

            // Group plants by location
            let plantsByLocation = {};
            plants.forEach(function(product) {
                if (!plantsByLocation[product.location]) {
                    plantsByLocation[product.location] = [];
                }
                plantsByLocation[product.location].push(product);
            });

            let contentHtml = '';
            let locationsHtml = `
                <div class="plant-location">
                    <button class="location-btn" data-location="all">Show All</button>
                </div>`;

            for (let location in plantsByLocation) {
                // Add plant items to contentHtml
                plantsByLocation[location].forEach(function(product) {
                    let imgPath = `Products/${product.seller_email}/${product.img1}`;
                    contentHtml += `
                        <div class="plant-item" data-location="${product.location}">
                            <div class="plant-image">
                                <img src="${imgPath}" alt="${product.plantname}">
                            </div>
                            <p>${product.plantname}</p>
                            <p>Price: ₱${product.price}</p>
                            <div class="plant-item-buttons">
                                <button class="view-details" data-id="${product.plantid}" data-email="${product.seller_email}">View Details</button>`;

                    // Debugging: Log userId and added_by values
                    console.log(`User ID: ${userId}, Product Added By: ${product.added_by}`);

                    // Only show "Chat Seller" button if user is not the seller
                    if (userId == 'null' || isLoggedIn && userId !== sellerUserId) {
                        contentHtml += `
                                <button class="chat-seller" data-email="${product.seller_email}">Chat Seller</button>`;
                    }

                    contentHtml += `
                            </div>
                        </div>`;
                });

                locationsHtml += `
                                <div class="plant-location">
                                    <button class="location-btn" data-location="${location}">
                                        ${location}
                                    </button>
                                </div>`;
                        }

                        $('#plants-categories').html(contentHtml);
                        $('#locations').html(locationsHtml);

                        // Add event listeners to location buttons to filter plants
                        $('.location-btn').on('click', function() {
                            let location = $(this).data('location');
                            console.log(`Button clicked: Location=${location}`); // Log the location

                            if (location === 'all') {
                                $('.plant-item').show();
                            } else {
                                $('.plant-item').each(function() {
                                    if ($(this).data('location') === location) {
                                        $(this).show();
                                    } else {
                                        $(this).hide();
                                    }
                                });
                            }
                        });

                      // Add event listeners to view-details and chat-seller buttons
                      $(document).on('click', '.view-details', function() {
                            let plantId = $(this).data('id');
                            let sellerEmail = $(this).data('email');

                            console.log(`View Details button clicked: Plant ID=${plantId}`); // Log the plant ID.
                            console.log(`Chat Seller button clicked: Seller Email=${sellerEmail}`); // Log the seller email

                            // Create a hidden form and submit it
                            let form = $('<form>', {
                                action: 'viewdetails?plant=' + plantId,
                                method: 'POST'
                            }).append($('<input>', {
                                type: 'hidden',
                                name: 'plantId',
                                value: plantId
                            })).append($('<input>', {
                                type: 'hidden',
                                name: 'sellerEmail',
                                value: sellerEmail
                            }));

                            $('body').append(form);
                            form.submit();

                            // Push the current state into the history when opening the modal
                            history.pushState(null, '', window.location.href);
                        });

                        $(document).on('click', '.chat-seller', function() {
                            let sellerEmail = $(this).data('email');
                            console.log(`Chat Seller button clicked: Seller Email=${sellerEmail}`); // Log the seller email
                        });
                    } catch (e) {
                        console.error("Error parsing JSON", e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An unexpected error occurred. Please try again later."
                });
                } 
                             
            });
</script>
</html>