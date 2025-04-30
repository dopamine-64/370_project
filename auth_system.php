<?php
// auth_system.php

session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Registration
if (isset($_POST['register'])) {
    $name      = $_POST['name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $user_type = $_POST['user_type'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, user_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $password, $phone, $user_type);
        if ($stmt->execute()) {
            $message = "Registration successful! Please login.";
        } else {
            $message = "Registration failed: " . $stmt->error;
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows == 1) {
        $stmt->bind_result($user_id, $name, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['name'] = $name;
            header("Location: dashboard.php");
            exit;
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "Email not registered.";
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth_system.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html>
<head>
    <title>To-Let Website - Auth System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 350px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        .logout-btn {
            background: crimson;
            margin-top: 20px;
        }
        .dashboard {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
<?php if ($page == 'dashboard'): ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <h2>Dashboard</h2>
        <p>Welcome, <strong><?php echo $_SESSION['name']; ?></strong>!</p>
        <p>You are logged in successfully.</p>
        <a href="auth_system.php?logout=true"><button class="logout-btn">Logout</button></a>
    <?php else: ?>
        <p>You must login first.</p>
        <a href="auth_system.php"><button>Go to Login</button></a>
    <?php endif; ?>
<?php else: ?>
    <h2>Login</h2>
    <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>
    <form action="auth_system.php" method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <hr>

    <h2>Sign Up</h2>
    <form action="auth_system.php" method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <select name="user_type" required>
            <option value="" disabled selected>Select Role</option>
            <option value="Owner">Owner</option>
            <option value="Tenant">Tenant</option>
            <option value="Both">Both</option>
        </select>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="register">Register</button>
    </form>
<?php endif; ?>
</div>

</body>
</html>
