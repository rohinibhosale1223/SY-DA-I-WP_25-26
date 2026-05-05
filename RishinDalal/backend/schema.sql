CREATE DATABASE IF NOT EXISTS jersey_adda;
USE jersey_adda;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    tag VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product Sizes & Inventory Table
CREATE TABLE IF NOT EXISTS product_sizes (
    product_id INT,
    size VARCHAR(10) NOT NULL,
    stock INT DEFAULT 0,
    PRIMARY KEY (product_id, size),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, -- Can be NULL if guest checkout is allowed
    full_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    zip VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    size VARCHAR(10) NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- -----------------------------------------------------
-- Seed Initial Product Data
-- -----------------------------------------------------

INSERT INTO products (id, name, price, image_url, tag) VALUES
(1, 'Modern Home Kit', 89.99, 'jersey_1_1777954650801.png', 'Bestseller'),
(2, 'Away Kit Edition', 85.00, 'jersey_2_1777954711453.png', 'New'),
(3, 'Classic Retro', 95.00, 'jersey_3_1777954728762.png', 'Limited'),
(4, 'Stealth Concept', 110.00, 'jersey_4_1777954743054.png', 'Premium'),
(5, 'Vibrant Third Kit', 90.00, 'jersey_5_1777954768591.png', ''),
(6, 'Pro Goalkeeper', 99.99, 'jersey_6_1777954965631.png', '');

-- Seed Initial Inventory (S, M, L, XL for all products)
INSERT INTO product_sizes (product_id, size, stock) VALUES
(1, 'S', 50), (1, 'M', 100), (1, 'L', 100), (1, 'XL', 50),
(2, 'S', 30), (2, 'M', 60),  (2, 'L', 60),  (2, 'XL', 20),
(3, 'S', 20), (3, 'M', 40),  (3, 'L', 40),  (3, 'XL', 10),
(4, 'S', 40), (4, 'M', 80),  (4, 'L', 80),  (4, 'XL', 30),
(5, 'S', 50), (5, 'M', 100), (5, 'L', 100), (5, 'XL', 50),
(6, 'S', 15), (6, 'M', 30),  (6, 'L', 30),  (6, 'XL', 10);
