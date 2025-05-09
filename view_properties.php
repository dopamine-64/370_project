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

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.8);
            padding: 14px 20px;
            display: flex;
           
            gap: 15px;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.5);
        }



        .navbar a {
            color: white;
            background-color: #007bff;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.3s;
        }

        .navbar a:hover {
            background-color: #0056b3;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 80px 20px 40px;
            position: relative;
            z-index: 1;
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
            margin-bottom: 40px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 0 25px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
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

        .dropdown-container {
            position: relative;
            display: inline-block;
            margin-top: 10px;
        }

        .dropdown-button {
            background-color: #444;
            color: #fff;
            padding: 8px 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 0;
            background-color: rgba(0, 0, 0, 0.95);
            max-width: 180px;
            margin-bottom: 10px;
            border-radius: 6px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.6);
            padding: 10px 14px;
            z-index: 999;
        }

        .dropdown-content a {
            color: #fff;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #555;
        }

        .dropdown-content a:hover {
            background-color: #555;
        }

        @media screen and (max-width: 768px) {
            .property-card { flex-direction: column; align-items: center; }
            .property-image { max-width: 100%; }
            .button-group { flex-direction: column; align-items: stretch; }
            .navbar { justify-content: center; flex-wrap: wrap; }
        }
    </style>
    <script>
        function confirmTenantSelection(form) {
            if (confirm("Are you sure you want to assign this tenant? This will make the post inactive.")) {
                form.submit();
            }
        }

        function toggleDropdown(id) {
            const dropdowns = document.querySelectorAll('.dropdown-content');
            dropdowns.forEach(d => {
                if (d.id === id) {
                    d.style.display = (d.style.display === 'block') ? 'none' : 'block';
                } else {
                    d.style.display = 'none';
                }
            });
        }

        document.addEventListener('click', function(event) {
            const isDropdownBtn = event.target.classList.contains('dropdown-button');
            const isInsideDropdown = event.target.closest('.dropdown-container');
            if (!isDropdownBtn && !isInsideDropdown) {
                document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = 'none');
            }
        });
    </script>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <a href="dashboard.php">Dashboard</a>
    <a href="post_property.php">Post Property</a>
</div>

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

                    <div class="button-group">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_id'] == $owner_id): ?>
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

                                    <div class="dropdown-container">
                                        <button class="dropdown-button" onclick="toggleDropdown('dropdown-<?php echo $property_id; ?>')">Chat with Applicants</button>
                                        <div id="dropdown-<?php echo $property_id; ?>" class="dropdown-content">
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
