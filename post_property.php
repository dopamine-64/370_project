<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth_system.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "tolet_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $address = $_POST['address'];
    $rent = $_POST['rent'];
    $available_from = $_POST['available_from'];
    $user_id = $_SESSION['user_id'];

    $image_path = "";
    if ($_FILES['image']['name']) {
        $image_path = "uploads/" . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    $stmt = $conn->prepare("INSERT INTO rooms (user_id, title, description, address, rent, available_from, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdss", $user_id, $title, $description, $address, $rent, $available_from, $image_path);

    if ($stmt->execute()) {
        $message = "Property posted successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post a Property</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 25px;
            width: 500px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #218838;
        }
        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Post a Property</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Property Title</label>
        <input type="text" name="title" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Address</label>
        <input type="text" name="address" required>

        <label>Rent (BDT)</label>
        <input type="number" name="rent" required>

        <label>Available From</label>
        <input type="date" name="available_from" required>

        <label>Property Image</label>
        <input type="file" name="image">

        <button type="submit">Post Property</button>
    </form>
</div>

</body>
</html>
