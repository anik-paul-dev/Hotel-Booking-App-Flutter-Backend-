CREATE DATABASE booking_hotel;
USE booking_hotel;

-- Rooms Table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price_per_night DECIMAL(10, 2) NOT NULL,
    description TEXT NOT NULL,
    features TEXT,
    facilities TEXT,
    max_adults INT NOT NULL,
    max_children INT DEFAULT 0,
    rating DECIMAL(3, 1) DEFAULT 0,
    area VARCHAR(50) NOT NULL,
    images TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name) -- Added for faster room lookups
);

-- Facilities Table
CREATE TABLE facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table (Moved up to support foreign key in bookings)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firebase_uid VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone_number VARCHAR(20) NOT NULL,
    picture VARCHAR(255) DEFAULT '',
    address TEXT DEFAULT '',
    pincode VARCHAR(10) DEFAULT '',
    date_of_birth VARCHAR(20) DEFAULT '',
    role varchar(50) DEFAULT 'user',  --later added
    is_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_firebase_uid (firebase_uid) -- Added for faster lookups
);

-- Bookings Table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) DEFAULT NULL,  -- Allow NULL values
    room_id INT NOT NULL,
    room_name VARCHAR(255) NOT NULL,
    price_per_night DECIMAL(10, 2) NOT NULL,
    check_in VARCHAR(50) NOT NULL,
    check_out VARCHAR(50) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    order_id VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('booked', 'cancelled') DEFAULT 'booked',
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(firebase_uid) ON DELETE SET NULL,
    INDEX idx_user_id (user_id)
);


-- Reviews Table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(255) DEFAULT NULL,  -- Allow NULL values
    user_name VARCHAR(255) NOT NULL,
    room_id INT NOT NULL,
    rating DECIMAL(3, 1) NOT NULL,
    review_text TEXT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(firebase_uid) ON DELETE SET NULL,
    INDEX idx_user_id (user_id)
);


-- Features Table
CREATE TABLE features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Carousel Images Table
CREATE TABLE carousel_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the `settings` table with timestamps
CREATE TABLE `settings` (
    id INT PRIMARY KEY DEFAULT 1, -- Fixed ID of 1 as per settings.php logic
    website_title VARCHAR(255) NOT NULL DEFAULT '',
    about_us_description TEXT NOT NULL DEFAULT '',
    shutdown_mode TINYINT NOT NULL DEFAULT 0, -- 0 or 1 for boolean-like flag
    contacts TEXT NOT NULL DEFAULT '[]', -- JSON-encoded array of contact objects
    social_media_links TEXT NOT NULL DEFAULT '[]', -- JSON-encoded array of social media objects
    team_members TEXT NOT NULL DEFAULT '[]', -- JSON-encoded array of team member objects
    image_url VARCHAR(255) NOT NULL DEFAULT '';
    website_name VARCHAR(255) NOT NULL DEFAULT '',
    phone_number VARCHAR(50) NOT NULL DEFAULT '',
    email VARCHAR(255) NOT NULL DEFAULT '',
    address TEXT NOT NULL DEFAULT '',
    facebook VARCHAR(255) NOT NULL DEFAULT '',
    twitter VARCHAR(255) NOT NULL DEFAULT '',
    instagram VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Auto-set on insert
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- Auto-update on change
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default row if needed (optional, since settings.php handles defaults)
INSERT INTO `settings` (`id`) VALUES (1)
ON DUPLICATE KEY UPDATE `id` = 1;

-- Queries Table
CREATE TABLE queries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    date DATETIME NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Added for consistency
);

ALTER TABLE bookings ADD COLUMN is_assigned TINYINT(1) DEFAULT 0 AFTER status;

--ALTER TABLE users MODIFY COLUMN picture VARCHAR(1024) DEFAULT '';