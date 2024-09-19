<?php
include 'conn.php';
session_start();
if(!isset($_SESSION['email'])){
$_SESSION['email'] = '';
}
session_destroy();
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
   <script src="node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    
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
                <a href="#">Home</a>
                <a href="#">About</a>
                <a href="#">Services</a>
                <a href="#">Contact</a>
            </div>
            <div class="login-signup">
                <a href="#" id="loginLink">Login</a>
            </div>
        </nav>

        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
    </div>

    <div class="dropdown-menu">
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Contact</a>
        <a href="#">Login</a>
    </div>

    <div class="featured">
        <p class="featured-header">
            Top Seller
        </p>
        <div class="featured-contents" id="featured-contents"></div>
    </div>

    <div class="newly-listed">
        <p class="newly-header">
            Newly Listed Plants
        </p>
        <div class="locations" id="locations">
            <!-- Locations -->
        </div>
        <div class="newly-contents" id="newly-contents">
            <!-- Products -->
        </div>
    </div>

   <!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Login</h2>
        <form method="POST" action="" id="loginForm">
            <input type="email" id="loginEmail" placeholder="Email" required>
            <input type="password" id="loginPassword" placeholder="Password" required>
            <button type="submit" value="login">Login</button>
        </form>
        <p>Don't have an account? <a href="#" id="signupLink">Sign Up</a></p>
    </div>
</div>

 <!-- Signup Modal -->
<div id="signupModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Sign Up</h2>
        <form method="POST" action="" id="signupForm">
            <input type="email" id="signupEmail" placeholder="Email" required>
            <input type="text" id="signupFirstName" placeholder="First Name" required>
            <input type="text" id="signupLastName" placeholder="Last Name" required>
            <input type="password" id="signupPassword" placeholder="Password" required>
            <input type="text" id="signupGender" placeholder="Gender" required>
            <input type="number" id="signupPhoneNumber" placeholder="Phone Number" required>
            <input type="textarea" id="signupAddress" placeholder="Address" required>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="#" id="loginLink">Login</a></p>
    </div>
</div>


    <script>
        
        document.querySelector('.hamburger').addEventListener('click', function() {
        const dropdownMenu = document.querySelector('.dropdown-menu');
        dropdownMenu.classList.toggle('show'); // Toggle the dropdown menu
    });
    // AJAX Fetching of top Seller
     $(document).ready(function() {
            $.ajax({
                url: 'Ajax/fetch_top_seller.php',
                type: 'GET',
                success: function(response) {
                    $('#featured-contents').html(response);

                    // Get the number of sellers from the hidden input
                    var numSellers = $('#num_sellers').val();

                    // Configure Splide depending on the number of sellers
                    if (numSellers > 1) {
                        // If more than 1 seller, use loop mode
                        new Splide('#seller-slider', {
                            type   : 'loop:slide',
                            perPage: 5,
                            autoplay: true,
                            gap: '1rem',
                            breakpoints: {
                                600: {
                                    perPage: 1,
                                },
                                900: {
                                    perPage: 2,
                                },
                            }
                        }).mount();
                    } else if (numSellers == 1) {
                        // If only 1 seller, use rewind mode (no looping)
                        new Splide('#seller-slider', {
                            type   : 'slide',
                            rewind : true,  // No loop, just slide back to the beginning
                            perPage: 1,     // Display 1 item
                            autoplay: false, // Disable autoplay
                        }).mount();
                    }
                }
            });
            // End of AJAX Fetching of top Seller

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
            // End of AJAX Fetching of newly listed plants

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
                                            <button class="view-details" data-id="${product.plantid}">View Details</button>
                                            <button class="chat-seller" data-email="${product.seller_email}">Chat Seller</button>
                                        </div>
                                    </div>`;
                            });

                            // Add location buttons to locationsHtml
                            locationsHtml += `
                                <div class="plant-location">
                                    <button class="location-btn" data-location="${location}">
                                        ${location}
                                    </button>
                                </div>`;
                        }

                        $('#newly-contents').html(contentHtml);
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
                            console.log(`View Details button clicked: Plant ID=${plantId}`); // Log the plant ID
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
                }
            });
            // End of AJAX Fetching of newly listed plants

    $("#loginForm").submit(function(event) {
    event.preventDefault();

    var email = $("#loginEmail").val();
    var password = $("#loginPassword").val();

    $.ajax({
        url: "Ajax/login.php",
        type: "POST",
        data: { email: email, password: password },
        success: function(response) {
            console.log("Response: " + response);
            if (response.trim() === "success") {
                Swal.fire({
                position: "center",
                icon: "success",
                title: "Successfully Logged in",
                showConfirmButton: false,
                timer: 3000
                });
                // Reload page after 1.5 seconds
                setTimeout(function() {
                    location.reload();
                }, 3000);
            } else {
                alert("Login failed! Please check your credentials.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + status + " - " + error);
        }
    });
});

$("#signupForm").submit(function(event) {
    event.preventDefault();

    var email = $("#signupEmail").val();
    var password = $("#signupPassword").val();
    var firstname = $("#signupFirstName").val();
    var lastname = $("#signupLastName").val();
    var gender = $("#signupGender").val();
    var phonenumber = $("#signupPhoneNumber").val();
    var address = $("#signupAddress").val();

    $.ajax({
        url: "Ajax/register.php",
        type: "POST",
        data: {
            email: email,
            password: password,
            firstname: firstname,
            lastname: lastname,
            gender: gender,
            phonenumber: phonenumber,
            address: address
        },
        success: function(response) {
            console.log("Response: " + response);
            if (response.trim() === "success") {
                alert("Signup successful!");
            } else {
                alert("Signup failed! Please check your inputs.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + status + " - " + error);
        }
    });
});
     
    });

        // Get the modals
        var loginModal = document.getElementById("loginModal");
        var signupModal = document.getElementById("signupModal");

        // Get the links to open the modals
        var signupLink = document.getElementById("signupLink");
        var loginLink = document.getElementById("loginLink");

        // Get the <span> element that closes the modals
        var closeButtons = document.getElementsByClassName("close");

        // When the user clicks on the signup link, open the signup modal
        signupLink.onclick = function() {
            loginModal.style.display = "none";
            signupModal.style.display = "block";
        }

        // When the user clicks on the login link, open the login modal
        loginLink.onclick = function() {
            signupModal.style.display = "none";
            loginModal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modals
        for (var i = 0; i < closeButtons.length; i++) {
            closeButtons[i].onclick = function() {
                loginModal.style.display = "none";
                signupModal.style.display = "none";
            }
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == loginModal || event.target == signupModal) {
                loginModal.style.display = "none";
                signupModal.style.display = "none";
            }
        }
    </script>
</body>
</html>