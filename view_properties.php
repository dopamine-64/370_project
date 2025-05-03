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
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        .property-card {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
        }

        .property-image {
            flex: 1;
            max-width: 250px;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border-radius: 10px;
        }

        .property-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .property-details {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .property-details h3 {
            margin-top: 0;
            color: #333;
        }

        .property-details p {
            margin: 5px 0;
            color: #555;
        }

        .property-details .rent {
            font-size: 18px;
            color: #28a745;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            gap: 10px;
        }

        .left-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn,
        .delete-btn,
        .rent-btn {
            height: 48px;
            padding: 0 20px;
            font-size: 14px;
            font-weight: bold;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .edit-btn { background-color: #007bff; }
        .edit-btn:hover { background-color: #0056b3; }

        .delete-btn { background-color: #dc3545; }
        .delete-btn:hover { background-color: #c82333; }

        .rent-btn { background-color: #28a745; font-size: 16px; width: 300px; }
        .rent-btn:hover { background-color: #218838; }

        select.applicants-dropdown {
            padding: 10px;
            width: 300px;
            font-size: 14px;
        }

        @media screen and (max-width: 768px) {
            .property-card {
                flex-direction: column;
            }

            .button-group {
                flex-direction: column-reverse;
                gap: 10px;
                align-items: flex-start;
            }

            .rent-btn,
            select.applicants-dropdown {
                width: 100%;
            }

            .left-buttons {
                justify-content: space-between;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Available Properties</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="property-card">
                <div class="property-image">
                    <?php if (!empty($row['image']) && file_exists($row['image'])): ?>
                        <img src="<?php echo $row['image']; ?>" alt="Property Image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/250?text=No+Image" alt="No Image">
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
                    </div>

                    <div class="button-group">
                        <div class="left-buttons">
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                                <a href="edit_property.php?property_id=<?php echo $row['property_id']; ?>" class="edit-btn">Edit</a>
                                <form action="delete_property.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this property?');">
                                    <input type="hidden" name="property_id" value="<?php echo $row['property_id']; ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <!-- Booking Request or Applicants Dropdown -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if ($_SESSION['user_id'] == $row['user_id']): ?>
                                <!-- Owner sees applicants -->
                                <?php
                                $property_id = $row['property_id'];
                                $app_query = "SELECT users.name FROM booking_requests 
                                              JOIN users ON booking_requests.user_id = users.user_id 
                                              WHERE booking_requests.property_id = $property_id";
                                $app_result = $conn->query($app_query);
                                ?>
                                <select class="applicants-dropdown">
                                    <option disabled selected>Applicants for this property</option>
                                    <?php if ($app_result->num_rows > 0): ?>
                                        <?php while ($app = $app_result->fetch_assoc()): ?>
                                            <option><?php echo htmlspecialchars($app['name']); ?></option>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <option>No applicants yet</option>
                                    <?php endif; ?>
                                </select>
                            <?php else: ?>
                                <!-- Not owner, show apply button -->
                                <form action="booking_requests.php" method="POST" style="margin-left: auto;">
                                    <input type="hidden" name="property_id" value="<?php echo $row['property_id']; ?>">
                                    <button type="submit" class="rent-btn">Apply for Tenant</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color:#777;">No properties posted yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
