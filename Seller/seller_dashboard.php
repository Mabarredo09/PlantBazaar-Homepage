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

<!--Start of Navbar -->
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
<!-- End of Navbar -->


<!-- Start of Main Content  -->
<div class="main-content">
    <h1>Welcome to Your Seller Dashboard</h1>
    <div class="product-list">
        <h2>Your Listed Plants</h2>
        <div class="card-container">
            <!-- Products will be dynamically inserted here -->
        </div>
    </div>
    
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Plant</h2>
            <form id="addProductForm" enctype="multipart/form-data">
                <label for="plantname">Plant Name:</label>
                <input type="text" id="plantname" name="plantname" required>

                <label for="plantcolor">Plant Color:</label>
                <input type="text" id="plantcolor" name="plantcolor" required>

                <label for="plantsize">Plant Size:</label>
                <input type="text" id="plantsize" name="plantsize" required>

                <label for="plantdetails">Plant Details:</label>
                <input type="text" id="plantdetails" name="plantdetails" required>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required min="0" step="0.01">

                <label for="plantcategories">Category:</label>
                <input type="text" id="plantcategories" name="plantcategories" required>

                <label for="img1">Image:</label>
                <input type="file" id="img1" name="img1" accept="image/*" required>

                <label for="img1">Image:</label>
                <input type="file" id="img1" name="img1" accept="image/*" required>

                <label for="img1">Image:</label>
                <input type="file" id="img1" name="img1" accept="image/*" required>

                <button type="submit">Add Product</button>
            </form>
            <div id="message"></div>
        </div>
    </div>
    <!-- <a href="add_product.php" id="openModalLink" class="add-product-btn">+ Add New Plant</a> -->
    <button type="button" id="openModalBtn1">Add New Plant</button>
        <!-- End of Main Content -->
         
    <!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Plant</h2>
        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="editPlantId" name="plantid"> <!-- Hidden field to hold plant ID -->
            <label for="editPlantname">Plant Name:</label>
            <input type="text" id="editPlantname" name="plantname" value="<?php echo $row['plantname']; ?>" required>

            <label for="editPlantsize">Plant Size:</label>
            <input type="text" id="editPlantSize" name="plantsize" required>

            <label for="editPlantcolor">Plant Color:</label>
            <input type="text" id="editPlantColor" name="plantcolor" required>

            <label for="editPlantcategories">Category:</label>
            <input type="text" id="editPlantcategories" name="plantcategories" required>

            <label for="editLocation">Location:</label>
            <input type="text" id="editLocation" name="plantlocation" required>

            <label for="editPrice">Price:</label>
            <input type="number" id="editPrice" name="price" required min="0" step="0.01">
            
            <div class="image-upload-container">

            <div class="image-upload-column">
            <label for="editImg1">1st Image:</label>
            <input type="file" id="editImg1" name="img1" accept="image/*">
            <span id="editImg1Label"></span>
            <img id="editImg1Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            <div class="image-upload-column">
            <label for="editImg2">2nd Image:</label>
            <input type="file" id="editImg2" name="img2" accept="image/*">
            <span id="editImg2Label"></span>
            <img id="editImg2Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            <div class="image-upload-column">
            <label for="editImg3">3rd Image:</label>
            <input type="file" id="editImg3" name="img3" accept="image/*">
            <span id="editImg3Label"></span>
            <img id="editImg3Preview" src="" alt="Image Preview" style="width: 100px; height: 100px;">
            </div>

            </div>
            <button type="submit">Update Product</button>
        </form>
        <div id="editMessage"></div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Confirm Deletion</h2>
        <p>Are you sure you want to delete this listing?</p>
        <button id="confirmDeleteButton">Yes, Delete</button>
        <button id="cancelDeleteButton">Cancel</button>
    </div>
</div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

  // Add event listeners to image inputs to preview images
document.getElementById('editImg1').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg1Preview').src = event.target.result;
      document.getElementById('editImg1Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('editImg2').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg2Preview').src = event.target.result;
      document.getElementById('editImg2Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});

document.getElementById('editImg3').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();
    reader.onload = function(event) {
      document.getElementById('editImg3Preview').src = event.target.result;
      document.getElementById('editImg3Label').innerHTML = file.name; // Display the file name next to the input field
    };
    reader.readAsDataURL(file);
});
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
            var imgSrc = '../Products/' + product.seller_email + '/' + product.img1;
                if (product.img1 === '') {
                    if (product.img2 !== '') {
                    imgSrc = '../Products/' + product.seller_email + '/' + product.img2;
                    } else if (product.img3 !== '') {
                    imgSrc = '../Products/' + product.seller_email + '/' + product.img3;
                    } else {
                    imgSrc = '../plant-bazaar.jpg' // Display a default image if all images are empty
                    }
  }
  
            card.append($('<img>').attr('src', imgSrc).attr('alt', product.plantname));
            card.append($('<p>').text('Price: ₱' + product.price));
            card.append($('<p>').text('Category: ' + product.plantcategories));
            
            // Create a container for the buttons
            var buttonContainer = $('<div>').addClass('button-container');

// Create the Edit button
var editButton = $('<button>')
    .addClass('edit-button') // Add a class for CSS styling and event handling
    .data('plantid', product.plantid) // Store the plant ID in a data attribute
    .text('Edit Listing') // Set the button text
    .css({
        backgroundColor: '#4CAF50', // Green background
        color: 'white', // White text
        border: 'none', // No border
        padding: '10px 15px', // Padding for the button
        textAlign: 'center', // Center text
        fontSize: '16px', // Font size
        margin: '4px 2px', // Margin around the button
        cursor: 'pointer', // Pointer cursor on hover
        borderRadius: '5px' // Rounded corners
    });

// Create the Delete button
var deleteButton = $('<button>')
    .addClass('delete-button') // Add a class for CSS styling
    .data('plantid', product.plantid) // Store the plant ID in a data attribute
    .text('Delete Listing') // Set the button text
    .css({
        backgroundColor: '#f44336', // Red background
        color: 'white', // White text
        border: 'none', // No border
        padding: '10px 15px', // Padding for the button
        textAlign: 'center', // Center text
        fontSize: '16px', // Font size
        margin: '4px 2px', // Margin around the button
        cursor: 'pointer', // Pointer cursor on hover
        borderRadius: '5px' // Rounded corners
    });

// Append buttons to the button container
buttonContainer.append(editButton);
buttonContainer.append(deleteButton);

// Append the buttonContainer to your product card
// Example: $('#productCard').append(buttonContainer);


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
$(document).ready(function() {
    // Get the modal
    var modal = $('#addProductModal');
    
    // Get the button that opens the modal
    var btn = $('#openModalBtn1');
    
    // Get the <span> element that closes the modal
    var span = $('.close');
    
    // When the user clicks the button, open the modal 
    btn.on('click', function(e) {
        e.preventDefault(); // Prevent default button behavior (stop navigating)
        modal.show(); // Show the modal
    });
    
    // When the user clicks on <span> (x), close the modal
    span.on('click', function() {
        modal.hide();
    });

    // When the user clicks anywhere outside of the modal, close it
    $(window).on('click', function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

    // Submit form via AJAX
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this); // Create FormData object from the form

        $.ajax({
            url: 'add_product.php', // URL to the PHP script that will handle the form submission
            type: 'POST',
            data: formData,
            contentType: false, // Tell jQuery not to set contentType
            processData: false, // Tell jQuery not to process the data
            success: function(response) {
                $('#message').html(response); // Display the response message
                $('#addProductForm')[0].reset(); // Reset the form
                modal.hide(); // Hide the modal after successful submission
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + status + " " + error);
                $('#message').html('<p style="color: red;">An error occurred while adding the product.</p>');
            }
        });
    });
});
$(document).ready(function() {
        // Get the modal
        var modal = $('#addProductModal');
        
        // Get the button that opens the modal
        var btn = $('#openModalBtn1');
        
        // Get the <span> element that closes the modal
        var span = $('.close');
        
        // When the user clicks the button, open the modal 
        btn.on('click', function(e) {
            e.preventDefault(); // Prevent default button behavior
            modal.show();
        });
        
        // When the user clicks on <span> (x), close the modal
        span.on('click', function() {
            modal.hide();
        });

        // When the user clicks anywhere outside of the modal, close it
        $(window).on('click', function(event) {
            if ($(event.target).is(modal)) {
                modal.hide();
            }
        });
    });

    $(document).on('click', '.edit-button', function() {
    var plantId = $(this).data('plantid'); // Get the plant ID
    // Fetch the existing data for this plant and populate the modal fields
    console.log('plantId:', plantId);
$.ajax({
  // ...
});
    $.ajax({
  url: 'fetch_listed_products.php', // URL to fetch product details
  type: 'GET',
  dataType: 'json',
  data: { plantid: plantId }, // Pass the plant ID
  success: function(data) {
    console.log(data);
    // Populate the modal fields with fetched data
    $('#editPlantId').prop('value', data.plantid);
    $('#editPlantname').prop('value', data.plantname);
    $('#editPrice').prop('value', data.price);
    $('#editPlantcategories').prop('value', data.plantcategories);
    $('#editPlantSize').prop('value', data.plantSize);
    $('#editPlantColor').prop('value', data.plantColor);
    $('#editLocation').prop('value', data.location);
    $('#editImg1Label').text(data.img1);
    $('#editImg2Label').text(data.img2);
    $('#editImg3Label').text(data.img3);

    // Open the edit modal
    $('#editProductModal').show();
  },
  error: function() {
    alert('Error fetching product data.');
  }
});
});

// Handle the delete button click
$(document).on('click', '.delete-button', function() {
    var plantId = $(this).data('plantid'); // Get the plant ID
    $('#deleteConfirmationModal').show(); // Open the delete confirmation modal

    // Set up the confirm button in the modal
    $('#confirmDeleteButton').off('click').on('click', function() {
        // Redirect to delete script
        window.location.href = 'delete_product.php?plantid=' + plantId;
    });
});

// Close modals when clicking on the close button
$('.close').on('click', function() {
    $('#editProductModal').hide();
    $('#deleteConfirmationModal').hide();
});

// Close the modals when clicking outside of them
$(window).on('click', function(event) {
    if ($(event.target).is('#editProductModal')) {
        $('#editProductModal').hide();
    } else if ($(event.target).is('#deleteConfirmationModal')) {
        $('#deleteConfirmationModal').hide();
    }
});

// Handle the form submission for updating the product
$('#editProductForm').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    var formData = new FormData(this); // Create FormData object from the form

    $.ajax({
        url: 'edit_product.php', // URL to the PHP script that will handle the form submission
        type: 'POST',
        data: formData,
        contentType: false, // Tell jQuery not to set contentType
        processData: false, // Tell jQuery not to process the data
        success: function(response) {
            $('#editMessage').html(response); // Display the response message
            $('#editProductForm')[0].reset(); // Reset the form
            $('#editProductModal').hide(); // Hide the modal after successful submission

            window.location.reload(); // Refresh the page
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: " + status + " " + error);
            $('#editMessage').html('<p style="color: red;">An error occurred while updating the product.</p>');
        }
    });
});

    </script>
</body>
</html>