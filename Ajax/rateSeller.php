<?php
session_start();
include '../conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to rate.']);
    exit;
}

// Get the input data and validate it
$sellerId = isset($_POST['sellerId']) ? intval($_POST['sellerId']) : 0;
$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

// Validate rating value
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5.']);
    exit;
}

// Check if the user has already rated this seller
$checkSql = "SELECT rating FROM ratings WHERE seller_id = ? AND user_id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ii", $sellerId, $userId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

// If a rating already exists, return an error message
if ($checkResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already rated this seller.']);
    exit;
}

// Insert the rating into the ratings table
$sql = "INSERT INTO ratings (seller_id, user_id, rating) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $sellerId, $userId, $rating);

if ($stmt->execute()) {
    // Calculate the new average rating
    $avgSql = "SELECT AVG(rating) as average_rating FROM ratings WHERE seller_id = ?";
    $avgStmt = $conn->prepare($avgSql);
    $avgStmt->bind_param("i", $sellerId);
    $avgStmt->execute();
    $avgResult = $avgStmt->get_result();
    $avgData = $avgResult->fetch_assoc();

    // Check if average rating was calculated
    if ($avgData['average_rating'] !== null) {
        // Update the average rating in the sellers table
        $updateSql = "UPDATE sellers SET ratings = ? WHERE seller_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $newAverage = round($avgData['average_rating'], 1); // Round to 1 decimal place
        $updateStmt->bind_param("di", $newAverage, $sellerId);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'average_rating' => $newAverage]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update seller rating.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to calculate average rating.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit rating.']);
}
?>