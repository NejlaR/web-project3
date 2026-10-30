CREATE DATABASE IF NOT EXISTS aurora_shoes
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE aurora_shoes;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL CHECK (price >= 0),
    image_url VARCHAR(255),
    category_id INT NOT NULL,
    stock INT NOT NULL DEFAULT 0 CHECK (stock >= 0),

    FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON DELETE RESTRICT
);

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM(
        'pending',
        'paid',
        'shipped',
        'completed',
        'cancelled'
    ) NOT NULL DEFAULT 'pending',
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0
        CHECK (total_amount >= 0),

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE RESTRICT
);

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL CHECK (quantity > 0),
    unit_price DECIMAL(10, 2) NOT NULL CHECK (unit_price >= 0),

    FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON DELETE CASCADE,

    FOREIGN KEY (product_id)
        REFERENCES products(product_id)
        ON DELETE RESTRICT
);

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id)
        REFERENCES products(product_id)
        ON DELETE CASCADE,

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);


INSERT INTO categories (name, description)
VALUES
    ('Sneakers', 'Comfortable shoes for everyday wear'),
    ('Running', 'Shoes for running and sports'),
    ('Boots', 'Boots for colder weather');

INSERT INTO products (
    name,
    description,
    price,
    image_url,
    category_id,
    stock
)
VALUES
    (
        'Aurora Classic',
        'Comfortable everyday sneakers',
        99.90,
        NULL,
        1,
        20
    ),
    (
        'Aurora Runner',
        'Lightweight running shoes',
        129.90,
        NULL,
        2,
        15
    ),
    (
        'Aurora Boot',
        'Comfortable winter boots',
        159.90,
        NULL,
        3,
        10
    );
    

-- Sample application users.
-- Demo password: password
-- These accounts are separate from the MySQL account aurora_app.

INSERT INTO users (full_name, email, password_hash, role)
VALUES (
    'Demo Admin',
    'admin@aurora.example',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',
    'admin'
);

INSERT INTO users (full_name, email, password_hash, role)
VALUES (
    'Demo Customer',
    'customer@aurora.example',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',
    'customer'
);

SET @customer_id = LAST_INSERT_ID();

SET @product_id = (
    SELECT product_id
    FROM products
    WHERE name = 'Aurora Classic'
    ORDER BY product_id
    LIMIT 1
);

INSERT INTO orders (user_id, status, total_amount)
VALUES (@customer_id, 'pending', 99.90);

SET @order_id = LAST_INSERT_ID();

INSERT INTO order_items (
    order_id,
    product_id,
    quantity,
    unit_price
)
VALUES (
    @order_id,
    @product_id,
    1,
    99.90
);

INSERT INTO reviews (product_id, user_id, rating, comment)
VALUES (
    @product_id,
    @customer_id,
    5,
    'Comfortable shoes for everyday wear.'
);