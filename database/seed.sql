USE food_ordering_system;

-- Initial Staff Accounts (Password for admin: admin123, for staff: staff123)
INSERT INTO staff (name, username, password, role, is_active) VALUES
('System Administrator', 'admin', '$2y$10$w6KzXhU1pI37R0k8JmsJhe09mD/FhV8i9sF.4r41pL5Q2.K7fH5.y', 'admin', 1),
('Canteen Cashier', 'staff', '$2y$10$sC7i6y1f6M/1h3t1tqL3ne0Y1f9R3E6S1gH2o8K6pZ4N8B0D2F4Hy', 'staff', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

INSERT INTO categories (name, description) VALUES
('Rice Meals', 'Rice meals and silog meals'),
('Viands', 'Home-cooked dishes and ulam'),
('Snacks', 'Light meals and merienda'),
('Drinks', 'Cold and hot beverages')
ON DUPLICATE KEY UPDATE description=VALUES(description);

INSERT INTO products
    (category_id, name, description, price, is_available)
VALUES
(1, 'Chicken Adobo Meal', 'Adobo served with rice', 65.00, 1),
(1, 'Pork Sisig Meal', 'Sisig served with rice', 75.00, 1),
(2, 'Pork Adobo', 'Classic Filipino pork adobo', 45.00, 1),
(2, 'Ginisang Gulay', 'Mixed vegetables', 30.00, 1),
(3, 'Pancit Canton', 'Stir-fried noodles', 25.00, 1),
(4, 'Iced Tea', 'Refreshing iced tea', 15.00, 1);