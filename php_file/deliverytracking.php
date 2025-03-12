<?php
session_start(); 
include 'navbar.php'; 
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Tracking</title>
    <style>
        
        .tracking-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
        .progress-bar-container {
            background-color: #f3f3f3;
            border-radius: 20px;
            padding: 10px;
            margin: 20px 0;
        }
        .progress-bar {
            height: 20px;
            background-color: #4caf50;
            width: 50%; /* Example progress; adjust dynamically based on tracking status */
            border-radius: 10px;
            transition: width 0.3s ease-in-out;
        }
        .order-details {
            margin-bottom: 20px;
        }
        .order-details span {
            display: block;
            margin: 5px 0;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container input[type="text"] {
            padding: 10px;
            width: 80%;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="tracking-container">
        <h2>Delivery Tracking</h2>
        <div class="form-container">
            <form method="POST" action="">
                <input type="text" name="order_id" placeholder="Enter your Order ID" required>
                <button type="submit">Track Order</button>
            </form>
        </div>
       <?php
             // Start session to access schedule_date
            include 'config.php';

            $schedule_date = isset($_SESSION['schedule_date']) ? $_SESSION['schedule_date'] : NULL; // Retrieve schedule date from session

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
                $order_id = $_POST['order_id'];

                // Prepare and execute the query
                $stmt = $conn->prepare("SELECT o.order_id, o.order_date, o.schedule_type, m.member_address 
                                        FROM orders o
                                        INNER JOIN members m ON o.member_id = m.member_id
                                        WHERE o.order_id = ?");
                $stmt->bind_param("s", $order_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $schedule_type = $row['schedule_type'];
                    $progress_width = $schedule_type == 'express' ? '50%' : '25%';

                    echo '<div class="order-details">';
                    echo '<span><strong>Order ID:</strong> ' . $row['order_id'] . '</span>';
                    echo '<span><strong>Order Date:</strong> ' . $row['order_date'] . '</span>';
                    echo '<span><strong>Schedule Type:</strong> ' . ucfirst($row['schedule_type']) . '</span>';
                    echo '<span><strong>Delivery Address:</strong> ' . $row['member_address'] . '</span>';

                    // Display the schedule date only for "schedule" type
                    if ($schedule_type === 'schedule' && !empty($schedule_date)) {
                        echo '<span><strong>Schedule Date:</strong> ' . htmlspecialchars($schedule_date) . '</span>';
                    }

                    echo '</div>';
                    echo '<div class="progress-bar-container">';
                    echo '<div class="progress-bar" style="width: ' . $progress_width . ';"></div>';
                    echo '</div>';
                } else {
                    echo '<p>No order found with the provided Order ID.</p>';
                }

                $stmt->close();
            }

            $conn->close();
            ?>


</body>
</html>
