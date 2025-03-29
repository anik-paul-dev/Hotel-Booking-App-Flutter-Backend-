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

-- Settings Table
CREATE TABLE settings (
    id INT PRIMARY KEY,
    website_title VARCHAR(255) NOT NULL,
    about_us_description TEXT NOT NULL,
    shutdown_mode TINYINT(1) DEFAULT 0,
    contacts JSON NOT NULL,
    social_media_links JSON NOT NULL,
    team_members JSON NOT NULL,
    website_name VARCHAR(255),
    phone_number VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert initial settings data with usable defaults
INSERT INTO settings (
    id, website_title, about_us_description, shutdown_mode, 
    contacts, social_media_links, team_members,
    website_name, phone_number, email, address, facebook, twitter, instagram
) VALUES (
    1, '', '', 0,
    '[]', '[]', '[]',
    '', '', '', '', '', '', ''
);

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