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
$updated = false;

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
        $updated = true;
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
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            width: 400px;
            margin: 80px auto;
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

        input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        input::placeholder {
            color: #aaa;
        }

        button {
            width: 100%;
            margin-top: 20px;
            background: #17a2b8;
            color: white;
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #138496;
        }

        .message {
            text-align: center;
            background: #28a745;
            padding: 10px;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            margin-top: 15px;
            display: none;
        }

        .message.show {
            display: block;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.6);
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
    <h2>Update Your Profile</h2>

    <?php if ($updated): ?>
        <div class="message show" id="popupMessage"><?php echo $message; ?></div>
        <script>
            setTimeout(function () {
                window.location.href = 'dashboard.php';
            }, 1000);
        </script>
    <?php elseif ($message): ?>
        <div class="message" style="background: #dc3545;"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>">

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" required value="<?php echo htmlspecialchars($phone); ?>">

        <button type="submit">Update Profile</button>
    </form>
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

</body>
</html>
