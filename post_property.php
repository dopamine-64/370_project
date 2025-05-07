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

    $stmt = $conn->prepare("INSERT INTO properties (user_id, title, description, address, rent, available_from, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: url('https://images.unsplash.com/photo-1580587771525-78b9dba3b914?fit=crop&w=1600&q=80') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .container {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
            width: 500px;
            margin: 50px auto;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 1px 1px 6px rgba(0, 0, 0, 0.6);
        }

        label {
            display: block;
            margin: 12px 0 6px;
            color: #ccc;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        input::file-selector-button {
            padding: 8px 12px;
            border: none;
            background: #17a2b8;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="file"] {
            color: #ccc;
        }

        button {
            background: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            margin-top: 15px;
        }

        button:hover {
            background: #218838;
        }

        .message {
            text-align: center;
            background: #28a745;
            padding: 10px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            margin-top: 15px;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 115%;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
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

<div class="overlay"></div>
<div class="container">
    <h2>Post a Property</h2>
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
        <script>
            setTimeout(function () {
                window.location.href = 'dashboard.php';
            }, 1000);
        </script>
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
    <a href="dashboard.php" class="back-link">? Back to Dashboard</a>
</div>

</body>
</html>
