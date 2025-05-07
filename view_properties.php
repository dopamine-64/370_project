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
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(3px);
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
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            gap: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            position: relative;
        }

        .property-image {
            flex: 1;
            max-width: 300px;
            border-radius: 12px;
            overflow: hidden;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .property-details {
            flex: 2;
            display: flex;
            flex-direction: column;
        }

        .property-details h3 {
            margin: 0 0 10px;
            font-size: 24px;
        }

        .property-details p {
            margin: 5px 0;
            font-size: 16px;
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
            align-items: center;
        }

        .button-group form,
        .button-group a {
            margin: 0;
        }

        .edit-btn,
        .delete-btn,
        .rent-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 12px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .edit-btn {
            background-color: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .rent-btn {
            background-color: #28a745;
            color: white;
            font-size: 16px;
        }

        .rent-btn:hover {
            background-color: #218838;
        }

        .applicants-dropdown {
            padding: 12px 16px;
            font-size: 16px;
            border: none;
            border-radius: 10px;
            background: rgba(255,255,255,0.8);
            color: #000;
            width: 100%;
            max-width: 150px;
            margin-left: 200px;
        }

        .applicants-dropdown:focus {
            outline: 2px solid #28a745;
        }

        @media screen and (max-width: 768px) {
            .property-card {
                flex-direction: column;
                align-items: center;
            }

            .button-group {
                flex-direction: column;
                align-items: stretch;
            }

            .property-image {
                max-width: 100%;
            }
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
                        <div class="left-buttons">
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                                <a href="edit.php?property_id=<?php echo $row['property_id']; ?>" class="edit-btn">Edit</a>
                                <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this property?');" style="display:inline;">
                                <input type="hidden" name="property_id" value="<?php echo $row['property_id']; ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>

                            <?php endif; ?>
                        </div>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_id'] == $row['user_id']): ?>
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
                                        <input type="hidden" name="property_id" value="<?php echo $row['property_id']; ?>">
                                        <button type="submit" class="rent-btn">Apply for Tenant</button>
                                    </form>
                                <?php else: ?>
                                    <p><em>You have already applied.</em></p>
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
