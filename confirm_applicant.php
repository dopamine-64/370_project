<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$owner_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['property_id']) && isset($_POST['selected_user_id'])) {
        $property_id = $_POST['property_id'];
        $selected_user_id = $_POST['selected_user_id'];

        // Verify the logged-in user owns the property
        $check_owner = $conn->prepare("SELECT * FROM properties WHERE property_id = ? AND user_id = ?");
        $check_owner->bind_param("ii", $property_id, $owner_id);
        $check_owner->execute();
        $owner_result = $check_owner->get_result();

        if ($owner_result->num_rows === 0) {
            die("You are not the owner of this property.");
        }

        // Start a transaction
        $conn->begin_transaction();

        try {
            // Set selected applicant to 'active'
            $activate = $conn->prepare("UPDATE booking_requests SET status = 'active' WHERE property_id = ? AND user_id = ?");
            $activate->bind_param("ii", $property_id, $selected_user_id);
            $activate->execute();

            // Set all others to 'inactive'
            $deactivate = $conn->prepare("UPDATE booking_requests SET status = 'inactive' WHERE property_id = ? AND user_id != ?");
            $deactivate->bind_param("ii", $property_id, $selected_user_id);
            $deactivate->execute();

            // Commit transaction
            $conn->commit();

            header("Location: view_properties.php?selection=success");
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            echo "Failed to confirm tenant: " . $e->getMessage();
        }
    } else {
        echo "Missing property_id or selected_user_id.";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
