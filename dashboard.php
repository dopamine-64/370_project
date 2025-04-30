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
<html>
<head>
    <title>Dashboard - To-Let Website</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f5f7fa;
        }
        header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .dashboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 30px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            width: 360px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 {
            margin-bottom: 10px;
        }
        .card p {
            margin: 6px 0;
            color: #444;
        }
        .dash-btn {
            display: inline-block;
            margin: 8px;
            padding: 10px 15px;
            background: #17a2b8;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .dash-btn:hover {
            background: #117a8b;
        }
        .logout-btn {
            background: crimson;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-btn:hover {
            background: #b30000;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to TO_LET</h1>
</header>

<div class="dashboard">
    <div class="card">
        <h3><?php echo htmlspecialchars($name); ?></h3>
        <p>Email: <?php echo htmlspecialchars($email); ?></p>
        <p>Phone: <?php echo htmlspecialchars($phone); ?></p>
        <p>Role: <?php echo htmlspecialchars($user_type); ?></p>
        <p>Joined: <?php echo date("F j, Y", strtotime($join_date)); ?></p>

        <div style="margin-top: 25px;">
            <a href="post_property.php" class="dash-btn">Post a Property</a>
            <a href="view_properties.php" class="dash-btn">View Available Properties</a>
            <a href="update_profile.php" class="dash-btn">Update Profile</a>
        </div>

        <form method="GET" action="auth_system.php">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>
    </div>
</div>

</body>
</html>
