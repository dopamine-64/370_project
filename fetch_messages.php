<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id']) || !isset($_GET['property_id'])) {
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_GET['receiver_id']);
$property_id = intval($_GET['property_id']);

$stmt = $conn->prepare("SELECT * FROM messages WHERE property_id = ? AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) ORDER BY sent_at ASC");
$stmt->bind_param("iiiii", $property_id, $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $class = ($row['sender_id'] == $sender_id) ? 'sent' : 'received';
    echo "<div class='message $class'>" . htmlspecialchars($row['message']) . "</div>";
}
?>
