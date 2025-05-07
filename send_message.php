<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Unauthorized.");
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$property_id = intval($_POST['property_id']);
$message = trim($_POST['message']);

if (!empty($message)) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, property_id, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $sender_id, $receiver_id, $property_id, $message);
    $stmt->execute();
}
?>
