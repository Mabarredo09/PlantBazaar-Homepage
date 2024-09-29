/* Modal Background */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #ffffff; /* White background for the modal */
    margin: 10% auto; /* Center the modal vertically and horizontally */
    padding: 20px;
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Subtle shadow */
    width: 80%; /* Width of the modal */
    max-width: 600px; /* Maximum width */
}

/* Close Button */
.close {
    color: #aaa; /* Gray color */
    float: right; /* Align to the right */
    font-size: 28px; /* Font size */
    font-weight: bold; /* Bold text */
}

/* Close Button Hover Effects */
.close:hover,
.close:focus {
    color: #000; /* Black when hovered */
    text-decoration: none; /* Remove underline */
    cursor: pointer; /* Pointer cursor */
}

/* Plant Details Content */
#plantDetailsContent {
    margin-top: 20px; /* Space above content */
}

#plantDetailsContent h3 {
    margin: 0; /* Remove default margin */
    font-size: 24px; /* Heading font size */
    color: #333; /* Dark text color */
}

#plantDetailsContent img {
    max-width: 100%; /* Responsive image */
    height: auto; /* Maintain aspect ratio */
    border-radius: 4px; /* Slight rounding of image corners */
}

#plantDetailsContent p {
    color: #555; /* Light gray text color */
    line-height: 1.6; /* Better line spacing */
}

/* Chat Button */
#chatButton {
    background-color: #28a745; /* Green background */
    color: white; /* White text */
    border: none; /* Remove border */
    padding: 10px 20px; /* Padding around text */
    border-radius: 5px; /* Rounded corners */
    cursor: pointer; /* Pointer cursor */
    margin-top: 20px; /* Space above button */
    transition: background-color 0.3s ease; /* Smooth background transition */
}

/* Chat Button Hover Effects */
#chatButton:hover {
    background-color: #218838; /* Darker green on hover */
}
