<?php
session_start();

// Redirect logged-in users to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to To-Let Properties</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden; /* Prevent scrolling */
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
            background: rgba(0, 0, 0, 0.6); /* Dark overlay */
            z-index: 1;
        }

        .header {
            position: absolute;
            top: 20px;
            left: 30px;
            display: flex;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
            z-index: 2;
        }

        .header img {
            width: 42px;
            height: 42px;
            margin-right: 10px;
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

        .content {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            backdrop-filter: blur(5px);
            max-width: 600px;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 15px;
            color: #fff;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #e0e0e0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        a.button {
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background 0.3s ease;
            box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.4);
        }

        a.button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div class="header">
    <img src="img/to-let.png" alt="To-Let Logo"> TO-LET
</div>

<div class="container">
    <div class="content">
        <h1>Welcome to TO-LET</h1>
        <p>Explore and post rental listings with ease. Whether you're looking for a place to stay or have a property to let, you're in the right spot.</p>
        <a href="auth_system.php" class="button">Login / Register</a>
    </div>
</div>

</body>
</html>
