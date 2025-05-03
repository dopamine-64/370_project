<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Registration
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $user_type = $_POST['user_type'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

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
    $email = $_POST['email'];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | To-Let Properties</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            backdrop-filter: blur(5px);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 1px 1px 5px rgba(0,0,0,0.7);
        }

        input, select, button {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border-radius: 6px;
            border: none;
            font-size: 15px;
        }

        input, select {
            background: rgba(255, 255, 255, 0.8);
            color: #333;
        }

        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .toggle-text {
            margin-top: 20px;
            font-size: 14px;
            color: #ccc;
        }

        .toggle-text a {
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            text-decoration: underline;
        }

        .message {
            margin-bottom: 10px;
            color: #ffdddd;
        }

        #register-form {
            display: none;
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="container">
    <div class="form-box">
        <h2 id="form-title">Login</h2>

        <?php if (isset($message)) echo "<div class='message'>$message</div>"; ?>

        <!-- Login Form -->
        <form id="login-form" method="POST" action="auth_system.php">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Register Form -->
        <form id="register-form" method="POST" action="auth_system.php">
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

        <div class="toggle-text">
            <span id="toggle-login">Don’t have an account? <a onclick="showRegister()">Register</a></span>
            <span id="toggle-register" style="display: none;">Already have an account? <a onclick="showLogin()">Login</a></span>
        </div>
    </div>
</div>

<script>
    function showRegister() {
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('register-form').style.display = 'block';
        document.getElementById('form-title').innerText = 'Register';
        document.getElementById('toggle-login').style.display = 'none';
        document.getElementById('toggle-register').style.display = 'block';
    }

    function showLogin() {
        document.getElementById('register-form').style.display = 'none';
        document.getElementById('login-form').style.display = 'block';
        document.getElementById('form-title').innerText = 'Login';
        document.getElementById('toggle-login').style.display = 'block';
        document.getElementById('toggle-register').style.display = 'none';
    }
</script>

</body>
</html>
