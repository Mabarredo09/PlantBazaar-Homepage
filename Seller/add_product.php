<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Modal and Form CSS -->
    <style>
        /* Modal Background */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
        }

        /* Modal Content Box */
        .modal-content {
            background-color: #fff;
            margin: 10% auto; /* 10% from the top and centered */
            padding: 2rem;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        /* Close Button */
        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        /* Form Inside Modal */
        #addProductForm {
            margin: 0; /* Remove margin */
        }

        /* Form Labels */
        #addProductForm label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #352208;
            font-size: clamp(1rem, 1vw, 1.5rem); /* Responsive font size */
        }

        /* Form Inputs */
        #addProductForm input[type="text"],
        #addProductForm input[type="number"],
        #addProductForm input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1rem;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        /* Focus effect for inputs */
        #addProductForm input:focus {
            background-color: #fff;
            border-color: #5B8C5A;
            outline: none;
        }

        /* Submit Button */
        #addProductForm button[type="submit"] {
            display: block;
            width: 100%;
            background-color: #386641;
            color: #FDFFFC;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: clamp(1rem, 1.2vw, 1.2rem);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #addProductForm button[type="submit"]:hover {
            background-color: #2d5233;
        }

        /* Message Display */
        #message {
            margin-top: 1rem;
        }
    </style>
</head>
<body>

    <!-- Button to Open Modal -->
    <button id="openModalBtn">Add New Plant</button>

    <!-- The Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Plant</h2>
            <form id="addProductForm" enctype="multipart/form-data">
                <label for="plantname">Plant Name:</label>
                <input type="text" id="plantname" name="plantname" required>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required>

                <label for="plantcategories">Category:</label>
                <input type="text" id="plantcategories" name="plantcategories" required>

                <label for="img1">Image:</label>
                <input type="file" id="img1" name="img1" accept="image/*" required>

                <button type="submit">Add Product</button>
            </form>
            <div id="message"></div>
        </div>
    </div>

    <!-- jQuery and JavaScript to Open/Close Modal and Handle AJAX Form Submission -->
    <script>
    $(document).ready(function() {
        // Get the modal
        var modal = $('#addProductModal');
        
        // Get the button that opens the modal
        var btn = $('#openModalBtn');
        
        // Get the <span> element that closes the modal
        var span = $('.close');
        
        // When the user clicks the button, open the modal 
        btn.on('click', function() {
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

        // Submit form via AJAX
        $('#addProductForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            var formData = new FormData(this); // Create FormData object from the form

            $.ajax({
                url: 'add_product_process.php', // URL to the PHP script that will handle the form submission
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
                    console.error("AJAX Error: " + status + error);
                    $('#message').html('<p style="color: red;">An error occurred while adding the product.</p>');
                }
            });
        });
    });
    </script>

</body>
</html>
