<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Apply for a property
    $property_id = $_POST['property_id'];

    // Check if already applied
    $check_sql = "SELECT * FROM booking_requests WHERE property_id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $property_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "You have already applied for this property.";
    } else {
        $insert_sql = "INSERT INTO booking_requests (property_id, user_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("ii", $property_id, $user_id);
        if ($stmt->execute()) {
            echo "Application submitted successfully.";
        } else {
            echo "Error: " . $conn->error;
        }
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['property_id'])) {
    // Fetch all applicants for a property
    $property_id = $_GET['property_id'];

    $sql = "SELECT users.name, users.email, booking_requests.requested_on
            FROM booking_requests
            JOIN users ON booking_requests.user_id = users.user_id
            WHERE booking_requests.property_id = ?
            ORDER BY booking_requests.requested_on DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<h3>Applicants</h3>";
    if ($result->num_rows > 0) {
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li><strong>{$row['name']}</strong> ({$row['email']}) - " . date('F j, Y, g:i a', strtotime($row['requested_on'])) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "No applicants yet.";
    }
    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
