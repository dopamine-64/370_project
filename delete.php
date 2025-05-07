<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['property_id'])) {
    $property_id = intval($_POST['property_id']);
    $user_id = $_SESSION['user_id'];

    // Check if the logged-in user is the owner of the property
    $check_query = "SELECT * FROM properties WHERE property_id = $property_id AND user_id = $user_id";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Delete associated booking requests first
        $conn->query("DELETE FROM booking_requests WHERE property_id = $property_id");

        // Then delete the property
        $delete_query = "DELETE FROM properties WHERE property_id = $property_id";
        if ($conn->query($delete_query) === TRUE) {
            $_SESSION['message'] = "Property deleted successfully.";
        } else {
            $_SESSION['message'] = "Error deleting property.";
        }
    } else {
        $_SESSION['message'] = "You are not authorized to delete this property.";
    }
}

header("Location: view_properties.php");
exit();
?>
