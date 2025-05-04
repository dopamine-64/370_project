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
$stmt = $conn->prepare("SELECT name, email, user_type, phone, join_date FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $user_type, $phone, $join_date);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - To-Let Website</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        body {
            background: url('https://images.unsplash.com/photo-1580587771525-78b9dba3b914?fit=crop&w=1600&q=80') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            color: white;
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

        header {
            position: relative;
            z-index: 2;
            width: 100%;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 36px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7);
        }

        .header-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .top-btn,
        .logout-btn {
            background: #17a2b8;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .top-btn:hover {
            background: #138496;
        }

        .logout-btn {
            background: crimson;
        }

        .logout-btn:hover {
            background: #b30000;
        }

        .container {
            position: relative;
            z-index: 2;
            width: 100%;
            height: calc(100% - 100px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 40px;
        }

        .card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            padding: 50px;
            border-radius: 16px;
            text-align: center;
            max-width: 500px;
            width: 95%;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .card h3 {
            margin-bottom: 12px;
            font-size: 28px;
            color: #fff;
        }

        .card p {
            margin: 8px 0;
            color: #ddd;
            font-size: 16px;
        }

        .card-actions {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .dash-btn {
            padding: 12px 24px;
            background: #17a2b8;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }

        .dash-btn:hover {
            background: #138496;
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<header>
    <div class="header-title">Welcome to TO_LET</div>
    <div class="header-buttons">
        <a href="update_profile.php" class="top-btn">Update Profile</a>
        <form method="GET" action="auth_system.php" style="margin: 0;">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </div>
</header>

<div class="container">
    <div class="card">
        <h3><?php echo htmlspecialchars($name); ?></h3>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Phone: <?php echo htmlspecialchars($phone); ?></p>
        <p>Role: <?php echo htmlspecialchars($user_type); ?></p>
        <p>Joined: <?php echo date("F j, Y", strtotime($join_date)); ?></p>

        <div class="card-actions">
            <a href="post_property.php" class="dash-btn">Post Property</a>
            <a href="view_properties.php" class="dash-btn">View Properties</a>
        </div>
    </div>
</div>

</body>
</html>
