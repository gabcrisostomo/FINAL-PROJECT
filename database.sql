CREATE DATABASE IF NOT EXISTS thread_trend;
USE thread_trend;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    contact VARCHAR(50) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_name VARCHAR(150) NOT NULL,
    activity TEXT NOT NULL,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

TRUNCATE TABLE products;
INSERT INTO products (name, category, price, stock, image) VALUES
('FEU NRMF Psychology Track Jacket', 'Outerwear', 899.00, 25, 'images/hoodie1.jpg'),
('Cuts and Crease Oni Button-Down (Black)', 'Shirts', 550.00, 30, 'images/tshirt1.jpg'),
('Cuts and Crease Daruma Button-Down (White)', 'Shirts', 550.00, 30, 'images/tshirt2.jpg');