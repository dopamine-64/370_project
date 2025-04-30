<?php
session_start();
$conn = new mysqli("localhost", "root", "", "tolet_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT properties.*, users.name AS posted_by FROM properties JOIN users ON properties.user_id = users.user_id ORDER BY posted_on DESC";
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
        }

        .property-image img {
            width: 100%;
            border-radius: 10px;
            object-fit: cover;
        }

        .property-details {
            flex: 2;
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
            gap: 10px;
            margin-top: 10px;
        }

        .buy-btn,
        .edit-btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }

        .buy-btn {
            background-color: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .edit-btn {
            background-color: #28a745;
            color: white;
        }

        .buy-btn:hover {
            background-color: #218838;
        }

        @media screen and (max-width: 768px) {
            .property-card {
                flex-direction: column;
            }

            .button-group {
                flex-direction: column;
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
                        <img src="https://via.placeholder.com/250x180?text=No+Image" alt="No Image">
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

                    <div class="button-group">
                        <form action="buy_property.php" method="POST" style="flex: 1;">
                            <input type="hidden" name="property_id" value="<?php echo $row['property_id']; ?>">
                            <button type="submit" class="buy-btn">Edit</button>
                        </form>

                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                            <a href="edit_property.php?property_id=<?php echo $row['property_id']; ?>" class="edit-btn">Buy</a>
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
