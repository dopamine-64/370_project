<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to To-Let Properties</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef3f8;
            text-align: center;
            padding: 50px;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            color: #007bff;
            margin-bottom: 10px;
        }

        p {
            font-size: 18px;
            color: #333;
            margin-bottom: 30px;
        }

        a.button {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s ease;
        }

        a.button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<?php
// Redirect logged-in users to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<div class="container">
    <h1>Welcome to To-Let Properties</h1>
    <p>Find or list rental properties in your city with ease.</p>

    <a href="auth_system.php" class="button">Login / Register</a>
</div>

</body>
</html>
