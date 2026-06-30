-- Beauty Shop Database
-- Import this file in phpMyAdmin before running the project.

CREATE DATABASE IF NOT EXISTS beauty_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE beauty_shop;

DROP TABLE IF EXISTS order_details;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30),
    address TEXT,
    role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(60) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    description TEXT,
    suitable_for VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    address TEXT NOT NULL,
    notes TEXT,
    status ENUM('Pending','Processing','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE order_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_details_order FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    CONSTRAINT fk_details_product FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (name, email, password, phone, address, role) VALUES
('Admin User', 'admin@beautyshop.test', '$2y$12$4pEcVZ4KpXpdLnCYj.f03OTnqgzRgypesvDi3twY4//Xk092QH.J6', '01700000000', 'Beauty Shop Office', 'admin');

INSERT INTO products (name, category, price, stock, image, description, suitable_for) VALUES
('Gentle Rose Face Cleanser', 'Skincare', 650.00, 25, 'assets/images/cleanser.svg', 'A mild daily face cleanser for removing dirt and oil without making the skin feel dry.', 'Normal to dry skin'),
('Matte Velvet Lipstick', 'Makeup', 480.00, 40, 'assets/images/lipstick.svg', 'Comfortable matte lipstick with rich color and long-lasting finish.', 'Daily makeup and party looks'),
('Herbal Repair Shampoo', 'Haircare', 720.00, 18, 'assets/images/shampoo.svg', 'A gentle shampoo designed to clean the scalp and support soft, healthy-looking hair.', 'Dry and rough hair'),
('Floral Mist Perfume', 'Perfume', 1200.00, 15, 'assets/images/perfume.svg', 'A light floral fragrance suitable for everyday use.', 'Daily wear'),
('SPF 50 Sun Protection Cream', 'Skincare', 850.00, 22, 'assets/images/sunscreen.svg', 'Lightweight sunscreen cream for daytime protection and a comfortable finish.', 'Outdoor use'),
('Hydrating Green Tea Toner', 'Skincare', 560.00, 30, 'assets/images/toner.svg', 'Refreshing toner that helps prepare the skin before moisturizer.', 'Oily and combination skin'),
('Smooth Cover Foundation', 'Makeup', 980.00, 16, 'assets/images/foundation.svg', 'Blendable foundation for a smooth and even-looking base.', 'Normal to combination skin'),
('Soft Touch Body Lotion', 'Personal Care', 540.00, 28, 'assets/images/lotion.svg', 'Moisturizing body lotion for soft and comfortable skin.', 'Daily body care');
