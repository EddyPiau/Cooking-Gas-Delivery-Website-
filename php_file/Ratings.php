<?php
// Database connection
include("config.php");

// Fetch customer ratings where PaymentMade = OrderTotal
$sql = "SELECT MemberName, CustomerRating 
        FROM member_details 
        WHERE PaymentMade = OrderTotal AND CustomerRating IS NOT NULL";
$result = $conn->query($sql);

$ratings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ratings[] = $row;
    }
}

// Return the ratings as JSON
echo json_encode($ratings);
$conn->close();
?>
