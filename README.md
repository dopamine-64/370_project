CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    user_type ENUM('Owner', 'Tenant', 'Both') NOT NULL,
    join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE properties (
    property_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    address VARCHAR(255) NOT NULL,
    rent DECIMAL(10,2) NOT NULL,
    available_from DATE,
    image VARCHAR(255),
    posted_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);


CREATE TABLE booking_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    status ENUM('pending', 'active', 'inactive') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    property_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(property_id) ON DELETE CASCADE
);


Instructions:
1. Open xampp and start Apache and MySQL.
2. Now click the MySQL Admin, it will open localhost in your browser.
3. Create a new database named tolet_db.
4. After that, click the sql tab and paste the code above and press go.
5. This will create the tables.
6. Now open the xampp folder, go to htdocs and create a new folder named To_let website.
7. Download all the files and move them to To_let website (C:\xampp\htdocs\To_let Website).
8. To open a flie in browser, copy this (http://localhost/To_let%20Website/index.php) and paste this in your browser.
9. Now you can browse the website.
