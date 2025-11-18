-- init_db.sql
CREATE DATABASE IF NOT EXISTS smartcart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smartcart;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  price INT NOT NULL,
  unit VARCHAR(50),
  image VARCHAR(500),
  category VARCHAR(100),
  discount INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  session_id VARCHAR(128) NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;


-- ei portion ta  chala ________________________________---


CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin user (username: admin, password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$GnvQxHkSXQiBUV57KT8ZzeeFHXn8pBfWHqfXeCL3xEeKECBUeIKtq');

-- ________________________________________________________-



-- sample products
INSERT INTO products (name, price, unit, image, category, discount) VALUES
('Fresh Bananas', 45, 'per dozen', '🍌', 'Fresh Produce', 10),
('Whole Milk', 65, '1L', '🥛', 'Dairy', 0),
('Brown Eggs', 120, '12 pieces', '🥚', 'Dairy', 15),
('Fresh Chicken', 280, '1kg', '🍗', 'Meat', 0),
('Basmati Rice', 85, '1kg', '🍚', 'Pantry', 0),
('Fresh Tomatoes', 35, '1kg', '🍅', 'Fresh Produce', 0),
('Bread Loaf', 25, '400g', '🍞', 'Bakery', 0),
('Orange Juice', 95, '1L', '🧃', 'Beverages', 0),
('Potatoes', 28, '1kg', '🥔', 'Fresh Produce', 0),
('Yogurt', 55, '500g', '🍦', 'Dairy', 0);

-- optional admin user (password: password)
INSERT INTO users (name, email, password, role) VALUES
('Admin User','admin@example.com','$2y$10$wH8p1s6bQy2a8Zx9a/2QGu2Q8P8c7g0z4qJq6QKgnldn0vY6J/3i2','admin');


-- _______________________________________________________________________________
CREATE TABLE `shop_categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(100) NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add these tables to your init_db.sql or run manually
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,                    -- NULL for guest (session-based)
    session_id VARCHAR(128) NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    total_amount INT NOT NULL,
    status ENUM('pending','confirmed','processing','delivered','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price INT NOT NULL,            -- price at the time of purchase
    discount_percent INT DEFAULT 0,
    final_price INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- _______________________________________________________________________________
