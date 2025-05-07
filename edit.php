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

if (!isset($_GET['property_id'])) {
    echo "Property ID not provided.";
    exit();
}

$property_id = intval($_GET['property_id']);
$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT * FROM properties WHERE property_id = ? AND user_id = ?");
$stmt->bind_param("ii", $property_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Property not found or you're not authorized to edit it.";
    exit();
}

$property = $result->fetch_assoc();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $rent = floatval($_POST['rent']);
    $available_from = $_POST['available_from'];

   
    $image_path = $property['image'];
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    $update = $conn->prepare("UPDATE properties SET title=?, description=?, address=?, rent=?, available_from=?, image=? WHERE property_id=? AND user_id=?");
    $update->bind_param("sssdsdii", $title, $description, $address, $rent, $available_from, $image_path, $property_id, $user_id);
    
    if ($update->execute()) {
        header("Location: view_properties.php");
        exit();
    } else {
        echo "Error updating property.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Property</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1580587771525-78b9dba3b914?fit=crop&w=1600&q=80') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(3px);
            z-index: -1;
        }

        .container {
            max-width: 600px;
            margin: 60px auto;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(6px);
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 32px;
            text-shadow: 2px 2px 6px #000;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 16px;
            margin-bottom: 15px;
        }

        input[type="text"]::placeholder,
        input[type="number"]::placeholder,
        textarea::placeholder {
            color: rgba(255,255,255,0.7);
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #ccc;
            text-decoration: none;
        }

        .back-link:hover {
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Property</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" name="title" required value="<?php echo htmlspecialchars($property['title']); ?>">

        <label for="description">Description</label>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($property['description']); ?></textarea>

        <label for="address">Address</label>
        <input type="text" name="address" required value="<?php echo htmlspecialchars($property['address']); ?>">

        <label for="rent">Rent (BDT)</label>
        <input type="number" name="rent" required min="0" value="<?php echo htmlspecialchars($property['rent']); ?>">

        <label for="available_from">Available From</label>
        <input type="date" name="available_from" required value="<?php echo $property['available_from']; ?>">

        <label for="image">Change Image (optional)</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Update Property</button>
    </form>

    <a href="view_properties.php" class="back-link">← Back to Properties</a>
</div>

</body>
</html>
