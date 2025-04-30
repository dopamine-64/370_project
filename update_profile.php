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

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch current user info
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $new_name, $new_email, $new_phone, $user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
        $name = $new_name;
        $email = $new_email;
        $phone = $new_phone;
    } else {
        $message = "Error updating profile.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            padding: 20px;
        }
        .container {
            background: white;
            width: 400px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Update Your Profile</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>

        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>
