<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT properties.*, users.name AS posted_by FROM properties 
        JOIN users ON properties.user_id = users.user_id 
        ORDER BY posted_on DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Properties</title>
    <style>
        body {
            margin: 0;
            padding: 0;
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
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
            z-index: -1;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 40px;
            text-shadow: 2px 2px 6px #000;
        }

        .property-card {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 0 25px rgba(0,0,0,0.5);
            position: relative;
        }

        .property-image {
            flex: 1;
            min-width: 280px;
            max-width: 300px;
            border-radius: 15px;
            overflow: hidden;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        .property-details {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .property-details h3 {
            font-size: 24px;
            margin: 0 0 10px;
        }

        .property-details p {
            margin: 2px 0;
            font-size: 15px;
            line-height: 1.4;
        }

        .rent {
            font-size: 18px;
            font-weight: bold;
            color: #00ff90;
        }

        .status {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
        }

        .chat-dropdown {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }

        .chat-dropbtn {
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
            padding: 10px 18px;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            backdrop-filter: blur(6px);
            transition: background-color 0.3s ease;
        }

        .chat-dropbtn:hover {
            background-color: rgba(0, 123, 255, 1);
        }

        .chat-dropdown-content {
            display: none;
            position: absolute;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            min-width: 220px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            z-index: 1;
            margin-top: 8px;
        }

        .chat-dropdown-content a {
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .chat-dropdown-content a:last-child {
            border-bottom: none;
        }

        .chat-dropdown-content a:hover {
            background-color: rgba(0, 123, 255, 0.15);
            color: #007bff;
        }

        .chat-dropdown:hover .chat-dropdown-content {
            display: block;
        }

        .dropdown-container {
            position: relative;
            display: inline-block;
            margin-top: 15px;
        }

        .dropdown-toggle {
            background-color: #6c63ff;
            color: white;
            padding: 12px 18px;
            font-size: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-weight: bold;
        }

        .dropdown-toggle:hover {
            background-color: #554dff;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: rgba(255, 255, 255, 0.95);
            min-width: 220px;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            top: 100%;
            left: 0;
            overflow: hidden;
        }

        .dropdown-menu a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background 0.2s ease;
        }

        .dropdown-menu a:hover {
            background-color: #eee;
        }

        .dropdown-container:hover .dropdown-menu {
            display: block;
        }



        .status.active { background: #28a745; }
        .status.pending { background: #ffc107; color: #000; }
        .status.inactive { background: #dc3545; }

        .button-group {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .edit-btn,
        .delete-btn,
        .rent-btn,
        .chat-btn {
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .edit-btn { background-color: #007bff; color: white; }
        .edit-btn:hover { background-color: #0056b3; }

        .delete-btn { background-color: #dc3545; color: white; }
        .delete-btn:hover { background-color: #c82333; }

        .rent-btn { background-color: #28a745; color: white; }
        .rent-btn:hover { background-color: #218838; }

        .chat-btn { background-color: #17a2b8; color: white; }
        .chat-btn:hover { background-color: #117a8b; }

        .applicants-dropdown {
            padding: 10px 14px;
            font-size: 15px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            width: 100%;
            max-width: 180px;
        }

        @media screen and (max-width: 768px) {
            .property-card { flex-direction: column; align-items: center; }
            .property-image { max-width: 100%; }
            .button-group { flex-direction: column; align-items: stretch; }
        }
    </style>
    <script>
        function confirmTenantSelection(form) {
            if (confirm("Are you sure you want to assign this tenant? This will make the post inactive.")) {
                form.submit();
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Available Properties</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <?php
            $property_id = $row['property_id'];
            $owner_id = $row['user_id'];

            $status_query = $conn->query("SELECT 
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_count,
                COUNT(*) AS total_count
            FROM booking_requests WHERE property_id = $property_id");
            $status_row = $status_query->fetch_assoc();
            $status = "active";
            if ($status_row['total_count'] > 0) {
                $status = ($status_row['active_count'] > 0) ? "inactive" : "pending";
            }
            ?>

            <div class="property-card">
                <div class="property-image">
                    <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                        <img src="<?php echo $row['image']; ?>" alt="Property Image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300?text=No+Image" alt="No Image">
                    <?php endif; ?>
                </div>
                <div class="property-details">
                    <div>
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($row['address']); ?></p>
                        <p class="rent"><strong>Rent:</strong> BDT <?php echo number_format($row['rent']); ?></p>
                        <p><strong>Available From:</strong> <?php echo htmlspecialchars($row['available_from']); ?></p>
                        <p><strong>Posted By:</strong> <?php echo htmlspecialchars($row['posted_by']); ?></p>
                        <p><strong>Posted On:</strong> <?php echo date('F j, Y', strtotime($row['posted_on'])); ?></p>

                        <div class="status <?php echo $status; ?>">
                            <?php echo ucfirst($status); ?>
                        </div>
                    </div>

                    <div class="button-group">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_id'] == $owner_id): ?>
                                <?php
                                $app_result->data_seek(0);
                                if ($app_result->num_rows > 0): ?>
                                    <div class="dropdown-container">
                                        <button class="dropdown-toggle">Chat with Applicants ▾</button>
                                        <div class="dropdown-menu">
                                            <?php while ($app = $app_result->fetch_assoc()): ?>
                                                <a href="chat.php?property_id=<?php echo $property_id; ?>&receiver_id=<?php echo $app['user_id']; ?>">
                                                    <?php echo htmlspecialchars($app['name']) . " ({$app['user_id']})"; ?>
                                                </a>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                                <a href="edit.php?property_id=<?php echo $property_id; ?>" class="edit-btn">Edit</a>
                                <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this property?');" style="display:inline;">
                                    <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>

                                <?php
                                $app_query = "SELECT booking_requests.*, users.name 
                                              FROM booking_requests 
                                              JOIN users ON booking_requests.user_id = users.user_id 
                                              WHERE booking_requests.property_id = $property_id";
                                $app_result = $conn->query($app_query);
                                ?>
                                <?php if ($status !== 'inactive' && $app_result->num_rows > 0): ?>
                                    <form method="POST" action="confirm_applicant.php" onsubmit="event.preventDefault(); confirmTenantSelection(this);">
                                        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                                        <select name="selected_user_id" class="applicants-dropdown" required>
                                            <option disabled selected>Select a tenant</option>
                                            <?php while ($app = $app_result->fetch_assoc()): ?>
                                                <option value="<?php echo $app['user_id']; ?>">
                                                    <?php echo htmlspecialchars($app['name']) . " ({$app['user_id']})"; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <button type="submit" class="rent-btn">Confirm Tenant</button>
                                    </form>
                                    <?php if ($status !== 'inactive' && $app_result->num_rows > 0): ?>
                                        <div class="chat-dropdown">
                                            <button class="chat-dropbtn">Chat with Applicants ⬇</button>
                                            <div class="chat-dropdown-content">
                                                <?php
                                                $app_result->data_seek(0); 
                                                while ($app = $app_result->fetch_assoc()):
                                                ?>
                                                    <a href="chat.php?property_id=<?php echo $property_id; ?>&receiver_id=<?php echo $app['user_id']; ?>">
                                                        <?php echo htmlspecialchars($app['name']) . " ({$app['user_id']})"; ?>
                                                    </a>
                                                <?php endwhile; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>


                                <?php elseif ($status === 'inactive'): ?>
                                    <p><em>Tenant already assigned.</em></p>
                                <?php else: ?>
                                    <p><em>No applicants yet.</em></p>
                                <?php endif; ?>

                            <?php else: ?>
                                <?php
                                $user_id = $_SESSION['user_id'];
                                $check = $conn->query("SELECT * FROM booking_requests WHERE user_id = $user_id AND property_id = $property_id");
                                ?>
                                <?php if ($check->num_rows == 0): ?>
                                    <form method="POST" action="booking_requests.php">
                                        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
                                        <button type="submit" class="rent-btn">Apply for Tenant</button>
                                    </form>
                                <?php else: ?>
                                    <p><em>You have already applied.</em></p>
                                    <a href="chat.php?property_id=<?php echo $property_id; ?>&receiver_id=<?php echo $owner_id; ?>" class="chat-btn">Chat with Owner</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; font-size: 18px;">No properties posted yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
